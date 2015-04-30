var stories = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	limit: 10,
	remote: {
		url: '/api/v1/search?q=%QUERY',
		cache: false,
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