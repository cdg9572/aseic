@extends('layouts.app')
@section('has_h1', true)

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
@endsection

@section('content')
<div class="inner">
	<section class="board_view">
		<article>
			<div class="tit_area">
				<h1 class="tit">2025 Global Eco-innovation Forum concluded successfully!</h1>
				<dl class="info">
					<div class="info_item"><dt>Date</dt><dd>2025.07.22</dd></div>
					<div class="info_item"><dt>Views</dt><dd>100</dd></div>
				</dl>
			</div>

			<div class="cont">
				<div class="tac"><img src="/images/img_sample_news_large.avif" alt="Event photo of 2025 Global Eco-innovation Forum"></div>
				<br/>
				The 2025 Global Eco-Innovation Forum concluded in Jeju on 2 September under the theme “Climate-Smart Innovations for Sustainable Local Economies.”<br/>
				<br/>
				A total of 29 ASEM and APEC member countries attended the forum on-site and online to exchange climate-smart agri-food policies and technologies, share MSME-led innovation cases, and chart pathways for global collaboration.<br/>
				<br/>
				We extend our sincere appreciation to the distinguished leaders and experts who contributed to the program.<br/>
				<br/>
				The forum received an overall satisfaction score of 4.7 out of 5.0, with 100% of respondents rating it “satisfactory” or higher. Participants particularly highlighted the caliber of speakers, the depth of panel dialogue, and the professional organization.<br/>
				<br/>
				ASEIC will build on these outcomes by advancing climate-smart technology adoption, expanding international cooperation, and supporting sustainable growth for MSMEs.<br/>
				<br/>
				Our sincere thanks to all who advanced MSME-led green innovation. We look forward to continued collaboration.
			</div>

			<div class="file_area" aria-label="Attached files">
				<a href="#this" download="Untitle.pdf" aria-label="Download attached file: Untitle.pdf"><strong>File</strong><p>Untitle.pdf</p></a>
				<a href="#this" download="Untitle.pdf" aria-label="Download attached file: Untitle.pdf"><strong>File</strong><p>Untitle.pdf</p></a>
			</div>
		</article>

		<nav class="prev_next" aria-label="Post navigation">
			<a href="#this" class="prev no_item"><strong>Previous</strong><p>No Previous Post</p></a>
			<a href="#this" class="next"><strong>Next</strong><p>Title of the next post</p></a>
		</nav>

		<div class="board_bottom flex_center">
			<a href="{{ route('announcements.index') }}" class="btn btn_wbb">Back to List</a>
		</div>
	</section>
</div>
@endsection