document.addEventListener('DOMContentLoaded', function () {
    const list = document.querySelector('[data-about-page-list]');
    if (!list) return;

    const entityName = list.dataset.entityName || '항목';
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = Array.from(document.querySelectorAll('.bo-row-checkbox'));

    selectAll?.addEventListener('change', function () {
        rowCheckboxes.forEach(function (checkbox) { checkbox.checked = selectAll.checked; });
    });
    rowCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (selectAll) selectAll.checked = rowCheckboxes.length > 0 && rowCheckboxes.every(function (item) { return item.checked; });
        });
    });
    document.querySelectorAll('[data-auto-submit]').forEach(function (select) {
        select.addEventListener('change', function () { select.form?.submit(); });
    });

    document.getElementById('btnDeleteMultiple')?.addEventListener('click', async function (event) {
        const button = event.currentTarget;
        const ids = rowCheckboxes.filter(function (checkbox) { return checkbox.checked; }).map(function (checkbox) { return Number(checkbox.value); });
        if (ids.length === 0) return window.alert('삭제할 '+entityName+'를 선택해주세요.');
        if (!window.confirm('선택한 '+ids.length+'개의 '+entityName+'를 삭제하시겠습니까?')) return;
        try { await aboutPageRequest(button.dataset.url, 'POST', { ids: ids }); window.location.reload(); } catch (error) {}
    });

    document.querySelectorAll('.btn-delete-about-page').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (!window.confirm('정말로 삭제하시겠습니까?')) return;
            try { await aboutPageRequest(button.dataset.url, 'DELETE'); window.location.reload(); } catch (error) {}
        });
    });
});

async function aboutPageRequest(url, method, payload) {
    try {
        const response = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', Accept: 'application/json' }, body: JSON.stringify(payload || {}) });
        const result = await response.json();
        if (!response.ok || result.success === false) throw new Error(result.message || '요청을 처리하지 못했습니다.');
        return result;
    } catch (error) {
        window.alert(error.message || '요청 처리 중 오류가 발생했습니다.');
        throw error;
    }
}
