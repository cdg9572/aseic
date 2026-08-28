# Backoffice 프로젝트 작업 지침

이 파일은 Codex가 프로젝트 작업 전에 읽는 최상위 지침이다. Cursor용 원본 규칙은 `.cursor/rules/`에 유지하며, 이 문서는 Codex에서 필요한 실행 원칙과 라우팅을 제공한다.

## 기본 원칙

- 기능 안정성, 가독성, 유지보수성을 우선한다.
- 기존 구조와 네이밍을 먼저 확인하고 일관되게 재사용한다.
- Controller, Service, Form Request, Model, Blade의 책임을 분리한다.
- 민감한 값은 코드에 하드코딩하지 않고 환경 설정으로 관리한다.
- 사용자 요청 범위를 벗어난 리팩터링이나 디자인 변경을 하지 않는다.
- Git 커밋, 푸시, PR 생성·병합은 사용자가 명시적으로 요청한 경우에만 수행한다.

## 요청과 변경 권한

- 설명, 검토, 진단 요청에는 근거를 제시하되 코드를 수정하지 않는다.
- `수정해줘`, `적용해줘`, `구현해줘`처럼 변경 의도가 명확하면 필요한 범위에서 구현하고 검증한다.
- DB 데이터, 인증·권한, 대규모 구조 변경처럼 되돌리기 어려운 작업은 영향과 계획을 먼저 설명하고 확인받는다.
- 마이그레이션 실행, `migrate:fresh`, `migrate:reset`, `migrate:rollback` 등 데이터 손실 가능 명령은 명시적 승인 없이 실행하지 않는다.

## Laravel 구조

- Controller는 요청 처리와 응답 반환에 집중한다.
- 비즈니스 로직은 `app/Services/`에 둔다.
- 유효성 검사는 Form Request로 처리한다.
- Model은 관계, 스코프, 액세서·뮤테이터 중심으로 유지한다.
- 관계 조회는 N+1 가능성을 확인하고 필요하면 eager loading을 사용한다.
- Blade에는 표시를 위한 간단한 조건과 반복만 남긴다.

관련 PHP 파일을 변경할 때 `.cursor/rules/10-laravel-architecture.mdc`를 참고한다.

## Blade와 정적 자산

- Blade 내부의 `<style>`, `<script>`, `style=`, `onclick=` 같은 인라인 코드 추가를 금지한다.
- CSS는 `public/css/`, JavaScript는 `public/js/`에서 관리한다.
- 기존 클래스, partial, component와 공용 자산을 새 코드보다 우선 재사용한다.
- 백오피스 신규 화면은 `resources/views/backoffice/board-posts/notices`의 구조와 기존 CRUD 패턴을 먼저 비교한다.
- 공용 레이아웃 클래스는 `bo-*` 접두사를 사용하고 도메인 전용 클래스는 필요한 경우에만 추가한다.

관련 화면 작업에서는 다음 규칙을 참고한다.

- `.cursor/rules/20-blade-and-assets.mdc`
- `.cursor/rules/25-backoffice-ui-consistency.mdc`
- `.cursor/rules/26-backoffice-naming-and-shared-crud.mdc`

## 개발 환경

- WSL Ubuntu, Docker, Laravel Sail, MySQL 구성을 기본 환경으로 해석한다.
- PHP, Composer, Artisan 명령은 프로젝트의 실제 컨테이너 상태를 확인한 뒤 적절한 실행 경로를 선택한다.
- 환경 설정은 `.env`와 Docker 구성 파일을 기준으로 확인하며 비밀값을 출력하거나 커밋하지 않는다.

## 검증과 완료 기준

- 변경 범위와 위험에 비례해 검증한다.
- PHP 변경 파일은 가능한 경우 구문 검사를 수행한다.
- 라우트, Controller 메서드, JavaScript endpoint 연결을 확인한다.
- Blade 변경 후 `style=`, `onclick=`, `<style`, `<script` 잔존 여부를 검색한다.
- CRUD나 목록 흐름을 변경한 경우 저장·취소·복귀, 유효성 실패, 필터와 query string 유지 여부를 확인한다.
- 실행하지 못한 검증은 통과로 간주하지 않고 사유와 남은 위험을 최종 보고에 적는다.

상세 하네스가 필요한 백오피스 변경에는 `backoffice-self-verification-harness` 스킬을 사용한다.

## 프로젝트 스킬

Codex용 스킬은 `.agents/skills/`에서 자동 검색된다. 이 저장소는 Cursor와 지침을 공유하기 위해 각 스킬을 `.cursor/skills/`의 원본에 연결한다.

- `backoffice-menu-scaffold`: 신규 백오피스 CRUD 메뉴와 화면 구성
- `backoffice-self-verification-harness`: 백오피스 변경 후 검증
- `laravel-refactor-checklist`: Laravel 계층 리팩터링
- `rules-conflict-audit`: Cursor/Codex 규칙의 중복과 충돌 감사

작업이 스킬 설명과 일치하면 해당 `SKILL.md` 전체를 읽고 적용한다.

## 서브에이전트 사용

- 사용자가 병렬 작업이나 서브에이전트를 요청하면 독립적인 하위 작업으로 나누어 사용한다.
- 복잡한 코드 탐색, 테스트, 규칙 감사처럼 읽기 중심이며 독립적인 작업은 병렬 위임할 수 있다.
- 여러 에이전트가 같은 파일을 동시에 수정하게 하지 않는다. 쓰기 작업은 담당 범위를 분리하거나 주 에이전트가 통합한다.
- 단순 변경이나 강하게 결합된 작업에는 서브에이전트를 사용하지 않는다.
- 서브에이전트 결과는 주 에이전트가 직접 검토하고 검증한 뒤 통합한다.

Cursor 전용 운영 예시는 `.cursor/subagents/README.md`에 있으며, Codex에서는 위 원칙과 현재 실행 환경의 에이전트 기능을 따른다.

## 규칙 우선순위

충돌 시 다음 순서를 따른다.

1. 시스템·개발자·사용자의 현재 지시
2. 현재 작업 디렉터리에 더 가까운 `AGENTS.override.md` 또는 `AGENTS.md`
3. 이 파일
4. 적용된 프로젝트 스킬
5. `.cursor/rules/`의 상세 프로젝트 관례

상위 지침과 충돌하는 하위 규칙은 적용하지 않는다.
