<label class="form-label">দোকান</label>
<select name="shop_id" class="form-select">
    <option value="">সব দোকান</option>
    @foreach ($shops as $s)
        <option value="{{ $s->shop_id }}">{{ trim($s->shop_name) }}</option>
    @endforeach
</select>
