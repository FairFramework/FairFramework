<div>
    <input assert="!{type}" id="input-{id}" type="text" class="form-control" value="{value}" />
    <input assert="{type}=text" id="input-{id}" type="text" class="form-control" value="{value}" />
    <select assert="{type}=bool" id="input-{id}" class="form-control">
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
    <ui assert="{type}=hash" type="form_hash" />
</div>
