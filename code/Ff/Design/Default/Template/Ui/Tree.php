<ul>
    <li>
        <span>
            <a href="$(system/base_url)@(uri)">@(label)</a>
        </span>
    </li>
    <ul if="@(items)" uiType="tree" dataCollection="items"></ul>
</ul>