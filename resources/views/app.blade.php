<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="_token" content="{{ csrf_token() }}" />

	<title>Collabowrite</title>

	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/site.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">

	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Cabin:400,700,400italic' rel='stylesheet' type='text/css'>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head> 
<body>
	<div id="primary-nav">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Collabowrite</a>
				</div>
	
				<div class="input-group search-group">
					<input type="text" class="form-control typeahead" placeholder="Search for a story...">
				</div>
	
				<!-- ko if: authed -->
				<ul class="navbar-envelope" data-bind="css: {'active': unreadReplies() > 0 }">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
					<div class="envelope-badge" data-bind="visible: unreadReplies() > 0, text: unreadReplies"></div>
				</ul>
				<!-- /ko -->
	
				<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
					</ul>
					
					
	
					<ul class="nav navbar-nav navbar-right">
						@if (Auth::guest())
							<li><a href="{{ url('/auth/login') }}">Login</a></li>
							<li><a href="{{ url('/auth/register') }}">Register</a></li>
						@else
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="{{ url('/user/'.Auth::user()->id) }}">Profile</a></li>
									<li><a href="{{ url('/auth/logout') }}">Logout</a></li>
								</ul>
							</li>
						@endif
					</ul>
				</div>
			</div>
		</nav>
		<div class="edit-panel hideOnLoad" id="editPanel">
			<div class="edit-panel-overlay"></div>
			<div class="edit-panel-content">
				<div class="edit-panel-list" data-bind="foreach: $root.envelopeReplies">
					<div class="edit-option">
						<div class="edit-option-title" data-bind="text: reply_user + ' replied to ' + sentence"></div>
						<div class="edit-option-desc" data-bind="text: reply"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@yield('content')

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-debug.js"></script>
	<script src="{{ asset('/marked.js') }}"></script>
	<script src="{{ asset('/typeahead.bundle.js') }}"></script>
	<script src="{{ asset('/handlebars.js')}}"></script>
	<script src="{{ asset('/search.js') }}"></script>
	<script type="text/javascript">
		var homeVM = function() {
			var self = this;
			self.authed = ko.observable({{ Auth::check() ? 'true' : 'false' }});
			self.envelopeReplies = ko.observableArray([]);
			
			self.unreadReplies = ko.computed(function() {
				var count = 0;
				for(var i = 0; i < self.envelopeReplies().length; i++) {
					var reply = self.envelopeReplies()[i];	
					if(!reply.read()) count++;		
				};
				return count;
			});
			
			<?php
				if(Auth::check()) {
					$replies = App\Models\ReplyReceipt::where('user_id', Auth::user()->id)->where('seen', false)->get();
					foreach($replies as $r) { ?>
						self.envelopeReplies.push({ id: {{$r->id}}, read: ko.observable(false), sentence: "{{$r->sentence()->content}}", reply_user: "{{$r->reply()->owner()->name}}", reply: "{{$r->reply()->content}}" });
					<?php }
				}
			?>
			$(".navbar-envelope").hover(function() {
				$(".edit-panel").show();
			}, function() {
				$(".edit-panel").hide();
				for(var i = 0; i < self.envelopeReplies().length; i++) {
					var reply = self.envelopeReplies()[i];
					if(!reply.read()) {
						reply.read(true);
						$.ajax('/api/v1/receipt/' + reply.id + '/read' , {
							method: 'POST',
							success: function(data) {
								console.log('Read receipt submitted!');
							},
							error: function(jqXHR, textStatus, errorThrown) {
								console.log('Read receipt error', jqXHR);
							}
						});
					}
				}
			});
		}
		
		ko.applyBindings(new homeVM(), document.getElementById('primary-nav'));
		
		$(function() {
			$.ajaxSetup({
		        headers: {
		            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
		        }
		    });
		});
	</script>

	@yield('script')
</body>
</html>
