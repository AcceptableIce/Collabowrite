@extends('app')

@section('script')
	<script type="text/javascript">
		function UserVM() {
			var self = this;

			self.name = ko.observable('{{$user->name}}');
			self.twitter = ko.observable('{{$user->profile()->twitter}}');
			self.website = ko.observable('{{$user->profile()->website}}');
			self.description = ko.observable($("#description-input").html());

			self.savedTwitter = '{{$user->profile()->twitter}}';
			self.savedWebsite = '{{$user->profile()->website}}';
			self.savedDescription = $("#description-input").html();

			self.editMode = ko.observable(false);

			self.enterEditMode = function() {
				self.editMode(true);
			}

			self.saveChanges = function() {
				self.editMode(false);

				if(self.twitter() != self.savedTwitter) {
					self.postChange('twitter', self.twitter());
					self.savedTwitter = self.twitter();
				}


				if(self.website() != self.savedWebsite) {
					self.postChange('website', self.website());
					self.savedWebsite = self.website();
				}

				if(self.description() != self.savedDescription) {
					self.postChange('description', self.description());
					self.savedDescription = self.description();
				}
			}

			self.postChange = function(key, value) {
				$.post('/api/v1/user/{{$user->id}}/profile', {field: key, content: value}, function() {
					console.log('posted.', key, value);
				});
			}

			self.escapeHtml = function(dirty) {
				var entityMap = {
					"&": "&amp;",
					"<": "&lt;",
					">": "&gt;",
					'"': '&quot;',
					"'": '&#39;',
					"/": '&#x2F;'
				};

				return String(dirty).replace(/[&<>"'\/]/g, function (s) {
					return entityMap[s];
				});
			}

			self.formatText = function(raw) {
				var clean = self.escapeHtml(raw);
				return marked(clean);
			}

			return self;
		}

		ko.applyBindings(new UserVM());
	</script>
@endsection

@section('content')
<div id="description-input">{{$user->profile()->description}}</div>
<div class="container user-container">
	@if ($user == Auth::user())
		<div class="row">
			<h1 data-bind="text: name"></h1>
			<h3>Twitter: </h3><h3 data-bind="visible: !editMode()"><a data-bind="attr: {href: 'https://twitter.com/' + twitter()}, text: twitter"></a></h3><input placeholder="@handle" type="text" data-bind="value: twitter, visible: editMode"></input>
			<h3>Website: </h3><h3><a data-bind="text: website, attr: { href: website }, visible: !editMode() && website">Website</a></h3><input class="block-field" placeholder="http://www.zombo.com" type="text" data-bind="value: website, visible: editMode"></input>
			<h3>Bio: </h3><div class="description" data-bind="html: formatText(description()), visible: !editMode()"></div><textarea class="description-box block-field" placeholder="This field supports Markdown formatting." data-bind="value: description, visible: editMode"></textarea>
		</div>
		<div class="row">
			<div class="button" data-bind="click: function() { enterEditMode() }, visible: !editMode()">Edit</div>
			<div class="button" data-bind="click: function() { saveChanges() }, visible: editMode()">Save</div>
		</div>
	@else
		<div class="row">
			<h1 data-bind="text: name"></h1>
			<h3><a data-bind="attr: {href: 'https://twitter.com/' + twitter()}, text: twitter"></a></h3>
			<h3><a data-bind="text: website, attr: { href: website }, visible: !website">Website</a></h3>
			<div class="description" data-bind="html: formatText(description())"></div>
		</div>
	@endif
</div>
@endsection
