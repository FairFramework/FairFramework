<ul class="list-unstyled list-group-item">
    <li>
        <p>@(label)</p>
        <div dataCollection="attributes/items">
            <div class="form-group">
                <label for="input-@(id)">@(label)</label>
                <input uiType="form_input" id="input-@(id)" type="text" class="form-control" value="@(value)" />
            </div>
        </div>
        <ul if="@(items)" uiType="form" dataCollection="items"></ul>
    </li>
</ul>