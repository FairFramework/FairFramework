<html lang="$(system/lang)">
    <head>
        <meta charset="$(system/charset)" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="/bootstrap.css" />
    </head>
    <body>
    <form id="page-form" enctype="multipart/form-data" action="" method="POST">
        <div id="page-container" class="container">
            <div id="page-header">
                <nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">$(admin/data/name)</a>
                    </div>
                    <div class=" collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav" data-collection="topmenu">
                            <li nif="@(has_items)">
                                <a nif="@(command)" href="#">@(title)</a>
                                <button if="@(command)" type="submit" class="btn btn-default">@(title)</button>
                            </li>
                            <li if="@(has_items)">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span>@(title)</span>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu" role="menu" data-collection="items">
                                    <li>@(title)</li>
                                </ul>
                            </li>
                        </ul>
                        <div class="navbar-form navbar-right">
                            <button type="submit" class="btn btn-default">Submit</button>
                        </div>
                    </div>
                </nav>
            </div>
            <div id="page-content" class="content">
                <div id="page-left" class="left"></div>
                <div id="page-middle" class="middle jumbotron">

                </div>
                <div id="page-right" class="right"></div>
            </div>
            <div id="page-footer" class="panel-footer"></div>
        </div>
    </form>
    <script type="text/javascript" src="/jquery.js"></script>
    <script type="text/javascript" src="/bootstrap.js"></script>
    </body>
</html>