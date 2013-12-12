<ul class="list-unstyled list-group-item">
    <li>
        <p>{label}</p>
        <div collection="attributes/items">
            <div class="form-group">
                <label for="input-@(id)">{label}</label>
                <ui type="form_input" id="input-{id}" class="form-control" value="{value}" />
            </div>
        </div>
        <ui assert="{items}" type="form" collection="items"></ui>
    </li>
</ul>