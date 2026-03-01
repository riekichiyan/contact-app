<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->with('category');

        $keyword = (string) $request->input('keyword', '');
        $keyword = trim(mb_convert_kana($keyword, 's'));

        if ($keyword !== '') {
            // 連続スペースを1つに
            $keyword = preg_replace('/\s+/', ' ', $keyword);

            $words = array_values(array_filter(explode(' ', $keyword), fn($w) => $w !== ''));

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

        /**
         * ② 性別
         */
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        /**
         * ③ お問い合わせ種別
         */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        /**
         * ④ 日付（from/to）
         */
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $contacts = $query
            ->orderByDesc('created_at')
            ->paginate(7)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    }

    public function show(Contact $contact)
    {
        $contact->load('category');

        return response()->json([
            'id' => $contact->id,
            'name' => $contact->last_name . ' ' . $contact->first_name,
            'gender' => $contact->gender,
            'email' => $contact->email,
            'tel' => $contact->tel,
            'address' => $contact->address,
            'building' => $contact->building,
            'category' => optional($contact->category)->content,
            'detail' => $contact->detail,
            'created_at' => optional($contact->created_at)->format('Y-m-d H:i'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $q = Contact::query()->with('category');

        $keyword = (string) $request->input('keyword', '');
        $keyword = trim(mb_convert_kana($keyword, 's'));

        if ($keyword !== '') {
            $keyword = preg_replace('/\s+/', ' ', $keyword);
            $words = array_values(array_filter(explode(' ', $keyword), fn($w) => $w !== ''));

            $q->where(function ($outer) use ($words) {
                foreach ($words as $word) {
                    $outer->where(function ($sub) use ($word) {
                        $sub->where('email', 'like', "%{$word}%")
                            ->orWhere('first_name', 'like', "%{$word}%")
                            ->orWhere('last_name', 'like', "%{$word}%")
                            ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", ["%{$word}%"])
                            ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$word}%"]);
                    });
                }
            });
        }

        /**
         * ② 性別
         */
        if ($request->filled('gender')) {
            $q->where('gender', $request->input('gender'));
        }

        /**
         * ③ カテゴリ
         */
        if ($request->filled('category_id')) {
            $q->where('category_id', $request->input('category_id'));
        }

        /**
         * ④ 日付（from/to）
         */
        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->input('to'));
        }

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');

            // Excel対策（UTF-8 BOM）
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['ID', 'お名前', '性別', 'メール', '問い合わせ種類', '作成日']);

            $q->orderByDesc('created_at')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $c) {
                    $genderText = match ((string) $c->gender) {
                        '1' => '男性',
                        '2' => '女性',
                        '3' => 'その他',
                        default => '',
                    };

                    fputcsv($out, [
                        $c->id,
                        $c->last_name . ' ' . $c->first_name,
                        $genderText,
                        $c->email,
                        optional($c->category)->content,
                        optional($c->created_at)->format('Y/m/d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename);
        
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', '削除しました。');
    }
}
