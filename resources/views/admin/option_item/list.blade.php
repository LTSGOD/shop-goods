<x-wire-table>
    {{-- 테이블 제목 --}}
    <x-wire-thead>
        <tr>
            <th width='20'>
                <input type='checkbox' class="form-check-input" wire:model="selectedall">
            </th>
            <th width='100'>enable</th>
            <th width='100'>name</th>
            <th width='100'>value</th>
            <th width='100'>stock</th>
            <th width='100'>price</th>
            <th width='100'>nested</th>
            <th width='100'>description</th>

        </tr>
    </x-wire-thead>
    <tbody>
        @if(!empty($rows))
            @foreach ($rows as $item)
            {{-- 테이블 리스트 --}}
            <x-wire-tbody-item :selected="$selected" :item="$item">

                <td width='100'>{{$item->enable}}</td>
                <td width='100'>{{$item->name}}</td>
                <td width='100'>{{$item->value}}</td>
                <td width='100'>{{$item->stock}}</td>
                <td width='100'>{{$item->price}}</td>
                <td width='100'>{{$item->nested}}</td>
                <td width='100'>{{$item->description}}</td>


            </x-wire-tbody-item>
            @endforeach
        @endif
    </tbody>
</x-wire-table>

