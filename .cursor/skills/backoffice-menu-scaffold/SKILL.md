---
name: backoffice-menu-scaffold
description: 기획서의 기능·필드는 보존하면서 members 기반 CRUD 레이아웃과 기존 관리자 시각 언어로 백오피스 메뉴를 구현한다. ASEIC 프로젝트 전용 메뉴, index/create/edit/withdrawn 구성, UI 일관성 유지 요청에 사용한다.
---

# Backoffice Menu Scaffold

## 목적

신규 백오피스 메뉴에서 기능 기획과 UI 기준을 분리한다. 필요한 데이터와 동작은 기획서를 따르고, 화면 구조와 시각 표현은 `members` 및 기존 공용 자산을 재사용해 디자인 드리프트를 방지한다.

## 적용 시점

- 새 백오피스 메뉴 구현
- ASEIC 관리자 기획서의 프로젝트 전용 메뉴 구현
- 기존 메뉴와 동일한 레이아웃으로 기능만 바꾸는 작업
- index/create/edit/withdrawn 화면의 UI 일관성 유지

## 기준 자료

- 기능: `docs/ASEIC글로벌포럼홈페이지_관리자_화면설계서_ver1_0_HK20260730.pptx`
- 목록: `resources/views/backoffice/members/index.blade.php`
- 등록·수정: `resources/views/backoffice/members/create.blade.php`, `edit.blade.php`, `_form.blade.php`
- 스타일: `public/css/backoffice/backoffice-crud.css`와 기존 공용 `board-*`, `bo-*`, `btn-*`

## 소스 우선순위

### 프로젝트 전용 메뉴

- 필드, 필수 여부, 상태값, 검색 항목, 연결 관계, 저장·노출 흐름은 기획서를 따른다.
- 색상, 간격, 버튼 모양·위치, 필터 배치, 테이블 형태는 기획서 화면을 모방하지 않는다.
- 기획서의 요구 항목을 `members` 화면 문법으로 번역한다.

### 공통 운영 모듈

홈페이지 설정, 팝업, 배너, 관리자, 권한그룹·권한 관리는 현재 구현을 우선한다. 기획서와 다르다는 이유만으로 기존 모듈을 재작성하지 않는다. 프로젝트 기능과의 연결에 필요한 최소 확장만 수행한다.

## UI 불변 조건

- 목록 상단 액션, 필터, Total·표시 개수, 테이블, 관리 버튼, pagination의 상대적 순서는 `members/index`를 따른다.
- 등록·수정의 목록 버튼, 섹션, 라벨·필드 행, 필수 표시, 하단 액션은 `members/create`, `edit`, `_form`을 따른다.
- 같은 행위의 기존 버튼 클래스, 색상, 크기, 아이콘을 그대로 재사용한다.
- 클래스와 셀렉터는 공용 `board-*`, `bo-*`, `btn-*`를 우선한다.
- 공용 스타일은 `public/css/backoffice/backoffice-crud.css`를 우선 사용한다.
- 임의 색상, 간격, 버튼 변형, 별도 카드·필터 레이아웃을 만들지 않는다.
- 도메인 전용 CSS는 기존 공용 패턴으로 표현할 수 없는 기능적 차이에만 추가한다.

## 실행 절차

1. 대상이 공통 운영 모듈인지 프로젝트 전용 메뉴인지 분류한다.
2. 프로젝트 전용 메뉴라면 관련 기획서 슬라이드에서 필드·행위·상태·관계를 추출한다.
3. `members`의 대응 화면을 열어 각 요구 항목을 기존 UI 구성요소에 매핑한다.
4. 인접 모듈의 route, Controller, Request, Service, Model, 자산 패턴을 확인한다.
5. partial/component와 공용 CSS·JavaScript 재사용 가능성을 먼저 확인한 뒤 구현한다.
6. route, Controller, 폼 action, JavaScript endpoint naming을 대조한다.
7. 완료 후 기능 요구와 UI 일관성을 분리해 검증한다.

## 구현 전 매핑

간단한 작업 메모로 다음을 정리한다.

| 기획 요구 | 데이터·동작 | 사용할 기존 UI 패턴 |
|---|---|---|
| 예: 노출 여부 | Y/N 저장 및 필터 | `_form` radio row + `index` filter group |

기획서에 있지만 대응 UI가 없을 때만 새 패턴을 검토한다. 새 패턴이 필요하면 기존 디자인 토큰과 버튼 의미를 유지한다.

## 검증

- 관련 기획서 항목이 빠짐없이 구현되었는가
- 공통 운영 모듈의 기존 동작을 불필요하게 변경하지 않았는가
- 버튼 위치·색상·크기·아이콘이 동일 행위의 기존 화면과 일치하는가
- 필터와 테이블 구조가 `members/index` 흐름을 따르는가
- 등록·수정 폼과 액션이 `members` 흐름을 따르는가
- 새 인라인 `style`, `onclick`, `<style>`, `<script>`가 없는가
- 도메인 전용 CSS·JavaScript가 꼭 필요한 범위로 제한되었는가

## 산출물 형식

- 생성·수정 파일 목록
- 반영한 기획서 항목과 관련 슬라이드
- 공용화하거나 재사용한 부분
- 도메인 특화로 분리한 부분
- UI 기준 화면과 달라진 예외 및 사유
- 검증 결과와 남은 위험
