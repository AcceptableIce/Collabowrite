@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<form class="new-prompt-input" method="POST" action="/api/v1/story">
			<h1>Have a story you'd like to tell?</h1>
			<input type="text" name="sentence" class="new-prompt-input-field" placeholder="Just write the first line." />
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />
			<input type="submit" class="button new-prompt-submit" value="&#8594;" />
		</form>
	</div>
	<h1 class="homepage-header">These 5 stories are fresh off the presses -- you'll LOL at number 4!</h1>
	@foreach(App\Models\Story::orderBy('created_at', 'DESC')->take(5)->get() as $story)
	<div class="row story-tier">
		<a href="/story/{{ $story->id }}"><span class="sentence">{{ $story->getRoot()->first()->content }}</span></a>
	</div> 
	@endforeach
	<hr/>
	<h1 class="homepage-header">These fire stories are begging for your attention!</h1>
	@foreach(DB::table('sentences')->select('story_id', DB::raw('count(*) as total'))->groupBy('story_id')->orderBy('total', 'DESC')->take(5)->get() as $data)
	<?php $story = App\Models\Story::find($data->story_id); ?>
	<div class="row story-tier">
		<a href="/story/{{ $story->id }}"><span class="sentence">{{ $story->getRoot()->first()->content }}</span></a>
	</div>
	@endforeach
</div>
@endsection

@section('script')
<script>
	function HomeVM() {
		var self = this;

	}
</script>
@endsection