# Forum view structure

행사별 사용자 화면은 아래 세 경로로 구분한다.

- 퍼블리싱 원본 확인: `/publishing-original` (`docs/aseic.zip` 기준 고정 스냅샷)
- 현재 작업 중인 default 확인: `/default`
- 실제 행사: `/{Main Page의 folder_name}`

Main Page의 `default` 항목과 `resources/views/forums/default`를 현재 사용자 화면 작업 기준으로 사용한다. `resources/views/forums/default`는 새로운 Main Page를 최초 생성할 때 입력한 폴더명으로 전체 복제된다.
복제된 행사 폴더는 이후 `default` 변경과 무관하게 보존하며, Main Page를 삭제해도 운영 중 직접 수정한 Blade 파일은 삭제하지 않는다.

`/publishing-original`은 `resources/views/publishing-original`의 전용 Blade만 사용하고, CSS·JavaScript·이미지는 `/publishing-original-assets/*`를 통해 `docs/aseic.zip`의 원본 파일을 직접 읽는다. 따라서 `resources/views/home`, `about`, `programme`, `archive`, `media`, `registration`, `announcements`와 `public`의 개발 자산을 변경해도 원본 미리보기에 반영되지 않는다.

`default`와 행사 폴더의 파일은 개발용 Blade를 include하는 행사별 진입점이다. 행사별 화면을 다르게 만들어야 할 때 해당 행사 폴더의 진입점을 독립 구현으로 교체한다.

`default`는 하나만 유지하는 작업 템플릿이다. `publishing-original`, `publishing-original-assets`, `forums`, `backoffice`, `auth`, `popup`과 공개 정적 자산 경로는 시스템 예약어라 새로운 Main Page의 폴더명으로 사용할 수 없다.
