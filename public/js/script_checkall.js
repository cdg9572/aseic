document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const termChecks = document.querySelectorAll('.term_check');

    if (!checkAll || termChecks.length === 0) return;

    // 1. 전체 동의 체크박스 클릭 시 -> 모든 개별 체크박스 상태 변경
    checkAll.addEventListener('change', function () {
        const isChecked = this.checked;
        termChecks.forEach(check => {
            check.checked = isChecked;
        });
    });

    // 2. 개별 체크박스 상태 변경 시 -> 전체 동의 상태 업데이트
    termChecks.forEach(check => {
        check.addEventListener('change', function () {
            // 모든 필수 체크박스가 체크되어 있는지 확인
            const allChecked = Array.from(termChecks).every(c => c.checked);
            checkAll.checked = allChecked;
        });
    });
});