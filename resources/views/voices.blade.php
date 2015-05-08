<!doctype html>
<html lang="en">
	<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://getbootstrap.com/examples/dashboard/dashboard.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/holder/2.6.1/holder.js"></script>
	<style>
		h1.voda {
			color: #BF0000;
		}
	</style>
	</head>

	<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
    	<div class="container-fluid">
        	<div class="navbar-header">
				<h1 class="voda">Voice of the Customer (Beta) </h1>
			</div>
		</div>
	</nav>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3 col-md-2 sidebar">
				<ul class="nav nav-sidebar">
		            <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a></li>
		            <li><a href="#">Whirlpool</a></li>
		            <li><a href="#">Facebook</a></li>
		            <li><a href="#">Twitter</a></li>
		            <li><a href="#">Youtube</a></li>
		        </ul>
			</div>
			<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
				<h1 class="page-header">Dashboard</h1>
				<div class="row placeholders">
				<div class="col-xs-6 col-sm-3 placeholder">
		            	<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		            	<h4>Total comments</h4>
		            	<span class="text-muted">{{count($statements)}}</span>
		            </div>
					<div class="col-xs-6 col-sm-3 placeholder">
		            	<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		            	<h4>Highest comment</h4>
		            	<span class="text-muted"><a href="{{$highest->url}}">{{$highest->sentiment}}</a></span>
		            </div>
		            <div class="col-xs-6 col-sm-3 placeholder">
		            	<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		            	<h4>Lowest comment</h4>
		            	<span class="text-muted"><a href="{{$lowest->url}}">{{$lowest->sentiment}}</a></span>
		            </div>
		            <div class="col-xs-6 col-sm-3 placeholder">
		            	<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		            	<h4>Average sentiment</h4>
		            	<span class="text-muted">{{$average}}</span>
		            </div>
				</div><!-- close row -->
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Sentiment</th>
								<th>Site</th>
								<th>Link</th>
								<th>Author</th>
								<th>Published</th>
								<th>Comment</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($statements as $statement)
							<tr>
								<td>{{$statement->sentiment}}</td>
								<td>{{$statement->site}}</td>
								<td><a href='{{$statement->url}}'>Direct link</a></td>
								<td>{{$statement->author}}</td>
								<td>{{ $statement->published->format('d-m-Y H:i:s') }}</td>
								<td>{{$statement->content}}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	</body>
</html>