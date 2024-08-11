<div>
    <!-- Sorting -->
    <div class="d-sm-flex align-items-center justify-content-between mt-n2 mb-3 mb-sm-4">
        <div class="fs-sm text-body-emphasis text-nowrap">Found <span class="fw-semibold">{{$foundItems}}</span> items</div>
        <div class="d-flex align-items-center text-nowrap">
            <label class="form-label fw-semibold mb-0 me-2">Sort by:</label>
            <div style="width: 190px">
                <select class="form-select border-0 rounded-0 px-1"
                wire:model.live="sorting">
                    {{-- <option value="Relevance">Relevance</option>
                    <option value="Popularity">Popularity</option> --}}
                    <option value="price:desc">Price: Low to High</option>
                    <option value="Price:asc">Price: High to Low</option>
                    <option value="created_at:desc">최신등록순</option>
                </select>
            </div>
        </div>
    </div>

    @foreach ($filters as $key => $filter)
        {{$key}} =
        @foreach ($filter as $i => $item)
        {{$i}}->{{$item}},
        @endforeach


    @endforeach

    <div class="row gy-4 gy-md-5 pb-4 pb-md-5">
        @foreach ($products as $product)

            @includeIf($viewCell, ['item' => $product])

        @endforeach
    </div>
</div>
