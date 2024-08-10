<?php
namespace Jiny\Shop\Goods\Http\Livewire;

use Illuminate\Support\Facades\Blade;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;


class ShopGoodsList extends Component
{
    use WithPagination;

    public $cate;
    public $display = "grid"; // "list"

    public $sorting;
    public $pagesize;
    //public $slug;
    public $category_id;
    public $cate_id;

    public $cartidx; // 카트번호

    public $viewGrid;
    public $viewList;
    public $viewCell;

    public $filters = [];
    public $options = [];

    public $foundItems;

    public function mount()
    {
        $this->sorting = "default";
        $this->pagesize = 12;

        if(!$this->viewGrid) {
            $this->viewGrid = "jiny-shop-goods::goods.grid";
        }

        if(!$this->viewList) {
            $this->viewList = 'jiny-shop-goods::goods.list';
        }

        if(!$this->viewCell) {
            $this->viewCell = "www::shop_fashion-v1._partials.goods.cell";
        }
    }

    public function render()
    {
        $paging = 16;
        $viewFile = $this->getViewFile($paging);
        return view($viewFile,[
            'products'=>$this->fetch($paging)
        ]);
    }

    private function fetch($paging)
    {
        $db = DB::table('shop_goods');

        // 조건필터 추가
        if($this->filters) {
            foreach($this->filters as $key => $filter) {
                // _시작하는 키의 경우 range 처리
                if($key[0] == '_') {
                    $field = trim($key,'_');
                    //dd($field);
                    if(isset($filter['range1']) && $filter['range1']) {
                        $db->where($field ,'>=', $filter['range1']);
                    }

                    if(isset($filter['range2']) && $filter['range2']) {
                        $db->where($field ,'<=', $filter['range2']);
                    }

                } else if($key[0] == '*') {
                    // or like
                    $field = trim($key,'*');
                    foreach($filter as $value) {
                        if($value) {
                            $db->orWhere($field, 'like', '%'.$value.';%');
                        }
                    }
                }
                else {
                    // $filer가 빈 배열이 아는 경우에만 처리
                    if($filter) {
                        $db->whereIn($key, $filter);
                    }
                }


                // foreach($filter as $value) {
                //     $db->whereOr($key,'like','%'.$value.'%');
                // }
            }
        }

        // 옵션 필터링
        if($this->options) {
            //dump($this->options);
            foreach($this->options as $key => $option) {
                foreach($option as $value) {
                    if($value) {
                        //dump($value);
                        $db->orWhere('option', 'like', '%'.$value.';%');
                    }
                }
            }
        }

        /*
        $category_id = $this->category_id;
        // 카테고리 상품 검색
        if($category_id) {
            $rows = $this->getCategory($category_id);
            $ids = [];
            foreach($rows as $row) {
                $ids []= $row->product_id;
            }

            $db = DB::table('shop_goods')->whereIn('id',$ids);

        } else {
            // 전체상품

        }
            */

        // 정렬방식 선택
        if($this->sorting == "date") {
            $db->orderBy('created_at',"DESC");
        } else
        if($this->sorting == "price") {
            $db->orderBy('regular_price',"ASC");
        } else
        if($this->sorting == "price-desc") {
            $db->orderBy('regular_price',"DESC");
        }

        // found Items
        $this->foundItems = $db->count();

        return $db->paginate($paging);
    }

    private function getCategory($category_id)
    {
        // 카테고리
        $row = DB::table('shop_product_categories')
            ->where('category_id', $category_id)
            ->first();
        return $row;
    }

    private function getViewFile()
    {
        if($this->display == "list") {
            return $this->viewList;
        } else {
            return $this->viewGrid;
        }
    }


    public function displayGrid()
    {
        $this->display = "grid";
    }

    public function displayList()
    {
        $this->display = "list";
    }

    #[On('reflash')]
    public function reflash()
    {
        // 이벤트로 화면을 갱신합니다.
    }


    /**
     * 관심상품 등록
     */
    public function addWish($id)
    {
        //dd($id);
        if(Auth::check()) {
            $email = Auth::user()->email;

            // 카트 갯수
            session()->increment('wish');

            // 신규상품 등록
            $product = DB::table('shop_goods')->where('id',$id)->first();

            if(!$product->name) {
                $product->name = "temp";
            }

            $data = [
                'email'=>$email,
                'product_id'=>$product->id,
                'product'=>$product->name,
                'image'=>$product->image,
                'price'=>$product->sale_price
            ];
            DB::table('shop_wish')->insert($data);

            // wish 컴포넌트 갱신
            //$this->emit('refreshComponent');
            $this->dispatch('refreshComponent');
        }
    }


    /**
     * 장바구니
     */
    public $popupCart = false;

    public function addCart($product_id, $product_name, $product_price)
    {
        if($this->cartidx) {

            // cart목록에 상품이 존재하는지 확인
            $cart = DB::table('shop_cart')
                ->where('cartidx',$this->cartidx)
                ->where('product_id',$product_id)->first();

            if($cart) {
                // 장바구니 존재 : 상품 갯수를 1개 증가
                DB::table('shop_cart')
                ->where('cartidx',$this->cartidx)
                ->where('product_id',$product_id)->increment('quantity');

            } else {
                // 카트 갯수
                session()->increment('cart');

                // 신규상품 등록
                $product = DB::table('shop_products')->where('id',$product_id)->first();
                $data = [
                    'cartidx'=>$this->cartidx,
                    'product_id'=>$product->id,
                    'product'=>$product->name,
                    'image'=>$product->image,
                    'price'=>$product->sale_price
                ];

                if(Auth::check()) {
                    $email = Auth::user()->email;
                    $data['email'] = $email;
                }

                DB::table('shop_cart')->insert($data);
            }
        }

        $this->popupCartOpen();
    }

    public function popupCartOpen()
    {
        $this->popupCart = true;
    }

    public function popupCartClose()
    {
        $this->popupCart = false;
    }

    /**
     * 이벤트
     */
    protected $listeners = [
        'setFilter', 'setOptions'
    ];

    public function setFilter($value)
    {
        foreach($value as $key => $item) {
            $this->filters[$key] = $item;
        }

    }

    public function setOptions($value)
    {
        foreach($value as $key => $item) {
            $this->options[$key] = $item;
        }

    }

}
