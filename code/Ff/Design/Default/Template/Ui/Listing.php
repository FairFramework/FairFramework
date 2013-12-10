<ul if="@(label)">
    <li>
        <span>
            <a href="$(system/base_url)@(uri)">@(label)</a>
        </span>
    </li>
    <ul if="@(items)" uiType="listing" dataCollection="items"></ul>
</ul>