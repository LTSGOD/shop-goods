<x-wire-table>
    {{-- 테이블 제목 --}}
    <x-wire-thead>
        <th width='200'>상품명</th>
        <th width='100'>타입</th>
        <th>가격</th>
        <th width='200'>담당자</th>
        <th width='200'>등록일자</th>
    </x-wire-thead>
    <tbody>
        @if(!empty($rows))
            @foreach ($rows as $item)
            {{-- 테이블 리스트 --}}
            <x-wire-tbody-item :selected="$selected" :item="$item">
                <td width='200'>
                    <a href="/admin/shop/products">
                        {{$item->goods}}
                    </a>
                </td>
                <td width='100'>
                    <a href="/admin/shop/price/type">
                        {{$item->type}}
                    </a>
                </td>
                <td>
                    <x-click wire:click="edit({{$item->id}})">
                        @if($item->enable)
                            {{$item->price}}
                        @else
                            <span style="text-decoration-line: line-through;">
                                {{$item->price}}
                            </span>
                        @endif
                    </x-click>
                </td>
                <td width='200'>
                    {{$item->manager}}
                </td>
                <td width='200'>
                    {{$item->created_at}}
                </td>
            </x-wire-tbody-item>
            @endforeach
        @endif
    </tbody>
</x-wire-table>

