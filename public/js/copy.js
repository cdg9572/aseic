/**
 * 주소 복사 기능 Script
 * .copy 클래스를 가진 버튼을 클릭하면 버튼 내부의 텍스트를 클립보드에 복사하고 알럿을 띄웁니다.
 * Modern Navigator Clipboard API와 구형/HTTP 환경을 위한 Fallback 방식을 지원합니다.
 */
document.addEventListener('DOMContentLoaded', function () {
    // .copy 클래스 버튼 가져오기 (페이지 내 여러 개가 존재할 경우 모두 대응)
    var copyButtons = document.querySelectorAll('.copy');

    if (copyButtons.length > 0) {
        copyButtons.forEach(function (copyBtn) {
            copyBtn.addEventListener('click', function () {
                var textToCopy = this.textContent.trim();
                copyToClipboard(textToCopy);
            });
        });
    }
});

/**
 * 텍스트 복사 실행 함수
 * @param {string} text - 복사할 텍스트
 */
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // HTTPS 환경 및 최신 브라우저 지원
        navigator.clipboard.writeText(text).then(function () {
            alert('주소가 클립보드에 복사되었습니다.');
        }).catch(function (err) {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        // HTTP 환경 또는 구형 브라우저 대응
        fallbackCopyTextToClipboard(text);
    }
}

/**
 * 구형 브라우저 / HTTP 환경 지원용 Fallback 함수
 * @param {string} text - 복사할 텍스트
 */
function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // 화면 스크롤 튀김 방지 설정
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            alert('주소가 클립보드에 복사되었습니다.');
        } else {
            alert('주소 복사에 실패했습니다.');
        }
    } catch (err) {
        alert('주소 복사에 실패했습니다.');
        console.error('클립보드 복사 에러:', err);
    }

    document.body.removeChild(textArea);
}