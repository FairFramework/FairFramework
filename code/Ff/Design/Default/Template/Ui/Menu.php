<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#topmenu">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">{label}</a>
    </div>
    <div class=" collapse navbar-collapse">
        <ul class="nav navbar-nav" collection="items">
            <li assert="!{items}">
                <a href="[system/base_url]{uri}">{menu/label}</a>
            </li>
            <li assert="{items}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span>{label}</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" collection="items">
                    <li>
                        <a href="[system/base_url]{uri}">@(label)</a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="navbar-form navbar-right">
            <button type="submit" class="btn btn-default">Submit</button>
        </div>
    </div>
</nav>