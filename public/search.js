var stories = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	limit: 10,
	prefetch: {
		url: '/api/v1/search',
		filter: function(list) {
			return $.map(list, function(entry) { return {id: entry.root[0].story_id, name: entry.root[0].content}; });
		}
	}
});

stories.initialize();

$('.typeahead').typeahead(null, {
	name: 'stories',
	displayKey: 'name',
	source: stories.ttAdapter(),
	templates: {
		empty: [
			'<div class="empty-message">No results found.</div>'
		].join('\n'),
		suggestion: Handlebars.compile('<a href="/story/{{ id }}">{{ name }}</a>')
	}
});