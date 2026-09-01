# Forum view structure

행사별 사용자 화면은 아래 세 경로로 구분한다.

- 퍼블리싱 원본 확인: `/publishing-original`
- 현재 작업 중인 default 확인: `/default`
- 실제 행사: `/{Main Page의 folder_name}`

Main Page의 `default` 항목과 `resources/views/forums/default`를 현재 사용자 화면 작업 기준으로 사용한다. `resources/views/forums/default`는 새로운 Main Page를 최초 생성할 때 입력한 폴더명으로 전체 복제된다.
복제된 행사 폴더는 이후 `default` 변경과 무관하게 보존하며, Main Page를 삭제해도 운영 중 직접 수정한 Blade 파일은 삭제하지 않는다.

현재 `default`와 행사 폴더의 파일은 기존 퍼블리싱 Blade를 include하는 행사별 진입점이다. 브라우저 원본 미리보기에는 행사 URL에서 동작시키기 위한 최소 라우팅 어댑터가 적용되며, 수정되지 않은 납품 원본은 `docs/aseic.zip`에 그대로 보관한다. 행사별 화면을 다르게 만들어야 할 때 해당 행사 폴더의 진입점을 독립 구현으로 교체한다.

`default`는 하나만 유지하는 작업 템플릿이다. `publishing-original`, `forums`, `backoffice`, `auth`, `popup`과 공개 정적 자산 경로는 시스템 예약어라 새로운 Main Page의 폴더명으로 사용할 수 없다.
