<ul class="list-unstyled list-group-item">
    <li>
        <div if="@(class)" class="form-group">
            <label for="exampleInputEmail1">@(label)</label>
            <input type="email" class="form-control" value="@(class)" />
        </div>
        <ul if="@(items)" uiType="form" dataCollection="items"></ul>
    </li>
</ul>