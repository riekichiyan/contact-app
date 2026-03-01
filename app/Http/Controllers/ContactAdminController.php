<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;          
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;


class ContactAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 共通フィルター
    |--------------------------------------------------------------------------
    */
    private function applyFilters(Request $request, $query): void
    {
        // --------------------
        // keyword
        // --------------------
        $keyword = (string) $request->input('keyword', '');
        $keyword = trim(mb_convert_kana($keyword, 's'));

        if ($keyword !== '') {
            $keyword = preg_replace('/\s+/', ' ', $keyword);
            $words = array_values(array_filter(explode(' ', $keyword)));

            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('email', 'like', "%{$word}%")
                            ->orWhere('first_name', 'like', "%{$word}%")
                            ->orWhere('last_name', 'like', "%{$word}%")
                            ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", ["%{$word}%"])
                            ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$word}%"]);
                    });
                }
            });
        }

        // --------------------
        // gender
        // --------------------
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // --------------------
        // category_id
        // --------------------
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // --------------------
        // 日付絞り込み
        // --------------------
        $from = $request->input('from');
        $to   = $request->input('to');

        if ($from || $to) {
            $fromDt = $from
                ? Carbon::parse($from)->startOfDay()
                : Carbon::create(1970, 1, 1)->startOfDay();

            $toDt = $to
                ? Carbon::parse($to)->endOfDay()
                : Carbon::now()->endOfDay();

            $query->whereBetween('created_at', [$fromDt, $toDt]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 一覧
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Contact::query()->with('category');

        $this->applyFilters($request, $query);

        $contacts = $query
            ->orderByDesc('created_at')
            ->paginate(7)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    } //
    /*
    |--------------------------------------------------------------------------
    | モーダル表示（詳細）
    |--------------------------------------------------------------------------
    */
    public function show(Contact $contact)
    {
        $contact->load('category');

        return response()->json([
            'id' => $contact->id,
            'name' => trim(($contact->last_name ?? '') . ' ' . ($contact->first_name ?? '')),
            'email' => $contact->email,
            'gender' => $contact->gender,
            'tel' => $contact->tel,
            'address' => $contact->address,
            'building' => $contact->building,
            'category' => optional($contact->category)->content,
            'detail' => $contact->detail,
            'created_at' => optional($contact->created_at)->format('Y-m-d H:i'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 削除
    |--------------------------------------------------------------------------
    */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', '削除しました。');
    }

    /*
    |--------------------------------------------------------------------------
    | 表プレビュー
    |--------------------------------------------------------------------------
    */
    public function exportPreview(Request $request)
    {
        $query = Contact::query()->with('category');

        $this->applyFilters($request, $query);

        $contacts = $query
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        return view('admin.export_preview', [
            'contacts' => $contacts,
            'query' => $request->query(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CSVダウンロード
    |--------------------------------------------------------------------------
    */
    public function export(Request $request): StreamedResponse
    {
        $query = Contact::query()->with('category');

        $this->applyFilters($request, $query);

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {

            $out = fopen('php://output', 'w');

        
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['ID', 'お名前', '性別', 'メール', '問い合わせ種類', '作成日']);

            $query->orderByDesc('created_at')
                ->chunk(200, function ($rows) use ($out) {
                    foreach ($rows as $c) {

                        $genderText = match ((string) $c->gender) {
                            '1' => '男性',
                            '2' => '女性',
                            '3' => 'その他',
                            default => '',
                        };

                        fputcsv($out, [
                            $c->id,
                            trim(($c->last_name ?? '') . ' ' . ($c->first_name ?? '')),
                            $genderText,
                            $c->email,
                            optional($c->category)->content,
                            optional($c->created_at)->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($out);
        }, $filename);
    }
}
