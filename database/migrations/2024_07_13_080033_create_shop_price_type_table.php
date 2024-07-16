<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * shop 상품 가격테이블
 * 상품이 가격 변동 이력을 관리 할 수 있습니다.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_price_type', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            # 상품 보이는지 활성화 여부
            $table->string('enable')->default(1);   // 활성화

            # 가격유형
            $table->string('name')->nullable();

            # 가격유형을 적용한 수량
            $table->string('prices')->nullable();

            # 가격지정 담당자
            $table->string('manager')->nullable();

            # 설명
            $table->text('description')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_prices');
    }
};
