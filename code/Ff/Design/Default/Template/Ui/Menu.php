<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#topmenu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">@(title)</a>
    </div>
    <div class=" collapse navbar-collapse">
        <ul class="nav navbar-nav" data-collection="items">
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