<div>
    <select assert="@(type)=(bool)" id="input-@(id)" class="form-control">
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
    <input assert="@(type)=()" id="input-@(id)" type="text" class="form-control" value="@(value)" />
    <input assert="@(type)=(hash)" uiType="form_hash" />
</div>
