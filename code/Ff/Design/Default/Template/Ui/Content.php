<ul class="list-unstyled list-group-item" dataCollection="attributes/items">
    <li>
        <div class="form-group">
            <label for="input@(id)">@(label)</label>
            <input id="input@(id)" type="text" class="form-control" value="@(value)" />
        </div>
        <ul uiType="content" dataCollection="items"></ul>
    </li>
</ul>