document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".js-detail");
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    const modal = document.getElementById("detailModal");
    const body = document.getElementById("modalBody");
    const deleteForm = document.getElementById("deleteForm");

    if (!modal || !body || !deleteForm) return;

    modal.style.display = "block";
    body.textContent = "読み込み中...";
    deleteForm.action = `/admin/contacts/${id}`;

    try {
        const res = await fetch(`/admin/contacts/${id}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();

        const genderText =
            data.gender_text ??
            (String(data.gender) === "1"
                ? "男性"
                : String(data.gender) === "2"
                    ? "女性"
                    : String(data.gender) === "3"
                        ? "その他"
                        : "");

        const categoryText = data.category_content ?? data.category?.content ?? "";
        const created = data.created_at_formatted ?? data.created_at ?? "";
        const updated = data.updated_at_formatted ?? data.updated_at ?? "";

        body.innerHTML = `
      <div class="modal-grid">
        <div class="modal-row"><div class="k">名前</div><div class="v">${data.last_name ?? ""} ${data.first_name ?? ""}</div></div>
        <div class="modal-row"><div class="k">性別</div><div class="v">${genderText}</div></div>
        <div class="modal-row"><div class="k">メール</div><div class="v">${data.email ?? ""}</div></div>
        <div class="modal-row"><div class="k">電話</div><div class="v">${data.tel ?? ""}</div></div>
        <div class="modal-row"><div class="k">住所</div><div class="v">${data.address ?? ""}</div></div>
        <div class="modal-row"><div class="k">建物名</div><div class="v">${data.building ?? ""}</div></div>
        <div class="modal-row"><div class="k">お問い合わせ種別</div><div class="v">${categoryText}</div></div>
        <div class="modal-row modal-row-wide"><div class="k">内容</div><div class="v">${(data.detail ?? "").replaceAll("\n", "<br>")}</div></div>
      </div>
    `;
    } catch (err) {
        console.error(err);
        body.textContent = "読み込みに失敗しました（Consoleを確認）";
    }
});

// 閉じる（×）
document.getElementById("modalClose")?.addEventListener("click", () => {
    const modal = document.getElementById("detailModal");
    if (modal) modal.style.display = "none";
});

// 背景クリックで閉じる
document.getElementById("detailModal")?.addEventListener("click", (e) => {
    if (e.target.id === "detailModal") e.currentTarget.style.display = "none";
});