<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;
use App\Http\Requests\StoreContactRequest;

class ContactController extends Controller
{
    // 入力画面
    public function create()
    {
        $draft = session('contact_draft', []);
        $categories = Category::orderBy('id')->get();

        return view('contacts.create', compact('draft', 'categories'));
    }

    // 確認画面
    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        session(['contact_draft' => $validated]);

        $genderLabel = match ((string)($validated['gender'] ?? '')) {
            '1' => '男性',
            '2' => '女性',
            '3' => 'その他',
            default => '',
        };

        $category = Category::find($validated['category_id'] ?? null);
        $categoryLabel = $category?->content ?? '';

        return view('contacts.confirm', [
            'inputs' => $validated,
            'genderLabel' => $genderLabel,
            'categoryLabel' => $categoryLabel,
        ]);
    }

    // 保存 → thanks
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        Contact::create($validated);

        session()->forget('contact_draft');

        return redirect()->route('contacts.thanks');
    }

    // 完了画面
    public function thanks()
    {
        return view('contacts.thanks');
    }

    // 管理画面（検索付き）
    public function index(Request $request)
    {
        $q = Contact::query();

        if ($request->filled('name')) {
            $name = $request->input('name');
            $q->where(function ($sub) use ($name) {
                $sub->where('first_name', 'like', "%{$name}%")
                    ->orWhere('last_name', 'like', "%{$name}%")
                    ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", ["%{$name}%"])
                    ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($request->filled('email')) {
            $email = $request->input('email');
            $q->where('email', 'like', "%{$email}%");
        }

        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->input('to'));
        }

        $contacts = $q->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('contacts.index', compact('contacts'));
    }
}
