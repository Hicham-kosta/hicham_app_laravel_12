<?php use App\Models\ProductsFilter; ?>
<div class="col-lg-3 col-md-12">
                
                <!-- Categories Filter Start -->
                   @php
                     // Get current main category (already passed in getCategoryListingData)
                     $mainCategory = $categoryDetails ?? null;
                     $selectedCategories = [];
                     if(request()->has('category')){
                        $selectedCategories = explode('~', request()->get('category'));
                     }
                   @endphp
                   @if(!empty($mainCategory) && $mainCategory->subcategories->count() > 0)
                    <div class="border-bottom mb-4 pb-4">
                        <h5 class="font-weight-semi-bold mb-4">Filter by Categories</h5>
                    <div>
                    @foreach($mainCategory->subcategories as $subCategory)
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-2">
                          <input type="checkbox" 
                          name="category"
                          id="category-{{$subCategory->id}}"
                          value="{{$subCategory->id}}"
                          class="custom-control-input filterAjax"   
                          {{in_array($subCategory->id, $selectedCategories) ? 'checked' : ''}}>
                          <label class="custom-control-label" for="category-{{$subCategory->id}}">
                            {{$subCategory->name}}
                          </label>
                        </div>
                    @endforeach
                    </div>
                 </div>
                @endif
                <!-- Categories filter End -->

                <!-- Price Start -->
                <div class="border-bottom mb-4 pb-4">
                    <h5 class="font-weight-semi-bold mb-4">Filter by Price</h5>
                    @php
                     $prices = ['0-1000', '1000-2000', '2000-5000', '5000-10000', '10000-100000'];
                     $selectedPrices = [];
                     if(request()->has('price')){
                         $selectedPrices = explode('~', request()->get('price'));
                     }
                     @endphp
                     <div>
                       @foreach($prices as $key => $price)
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-
                          content-between mb-2">
                          <input type="checkbox"
                             name="price"
                             id="price{{$key}}"
                             value="{{$price}}"
                             class="custom-control-input filterAjax"
                             {{ in_array($price, $selectedPrices) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="price{{$key}}">₹{{$price}}</label>
                        </div>
                      @endforeach
                </div>
              </div>
                <!-- Price End -->
                
                <!-- Color Start -->
                <div class="border-bottom mb-4 pb-4">
                    <h5 class="font-weight-semi-bold mb-4">Filter by Color</h5>
                    @php
                      $getColors = ProductsFilter::getColors($catIds);
                      $selectedColors = [];
                      if(request()->has('color')){
                        $selectedColors = explode('~', request()->get('color'));
                      }
                    @endphp
                    <div>
                        @foreach($getColors as $key => $color)
                          <div class="custom-control custom-checkbox d-flex align-items-center justify-
                          content-between mb-2">
                            <input type="checkbox"
                             name="color"
                             id="color{{$key}}"
                             value="{{$color}}"
                             class="custom-control-input filterAjax"
                             {{ in_array($color, $selectedColors) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="color{{$key}}">{{ucfirst($color)}}</label>
                          </div>
                        @endforeach
                    </div>
                </div>
                <!-- Color End -->

                <!-- Size Start -->
                <div class="border-bottom mb-4 pb-4">
                    <h5 class="font-weight-semi-bold mb-4">Filter by Size</h5>
                    @php
                      $getSizes = ProductsFilter::getSizes($catIds);
                      $selectedSizes = request()->has('size') ? explode('~', request()->get('size')) : [];
                    @endphp
                    <div>
                        @foreach ($getSizes as $key => $size)
                          <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-2">
                            <input type="checkbox"
                            name="size"
                            id="size{{$key}}"
                            value="{{$size}}"
                            class="custom-control-input filterAjax"
                            {{ in_array($size, $selectedSizes) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="size{{$key}}">{{strtoupper($size)}}</label>
                          </div>
                        @endforeach  
                    </div> 
                </div>
                <!-- Size End -->
                
                <!-- Brand Start -->
                  <div class="border-bottom mb-4 pb-4">
                     <h5 class="font-weight-semi-bold mb-4">Filter by Brand</h5>
                      @php
                      $getBrands = ProductsFilter::getBrands($catIds);
                      $selectedBrands = [];
                      if(request()->has('brand')){
                        $selectedBrands = explode('~', request()->get('brand'));
                      }
                      @endphp
                      <div>
                        @foreach($getBrands as $key => $brand)
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-2">
                          <input type="checkbox"
                            name="brand"
                            id="brand{{$key}}"
                            value="{{$brand['name']}}"
                            class="custom-control-input filterAjax"
                            {{ in_array($brand['name'], $selectedBrands) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="brand{{$key}}">{{ucfirst($brand['name'])}}</label>
                        </div>
                        @endforeach
                      </div>
                  </div>
                <!-- Brand End -->

                <!-- Dynamic Filters Start -->
                @foreach($filters as $filter)
                @php
                // Get values already sorted by sort from eager loading
                  $filtersValues = $filter->values
                  ->where('status', 1)
                  ->filter(function($value) use ($catIds) {
                    // keep only those linked to products in these categories
                    return $value->products->whereIn('category_id', $catIds)->isNotEmpty();
                  });
                  if ($filtersValues->isEmpty()) continue;
                  $selectedValues = request()->has($filter->filter_name) 
                  ? explode('~', request()->get($filter->filter_name)) 
                  : [];
                @endphp

                <div class="border-bottom mb-4 pb-4">
                  <h5 class="font-weight-semi-bold mb-4">Filter by {{ucwords($filter->filter_name)}}</h5>
                  <div>
                    @foreach($filtersValues as $key => $valueObj)
                     <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-2">
                      <input type="checkbox"
                        name="{{$filter->filter_name}}"
                        id="{{$filter->filter_name}}{{$key}}"
                        value="{{$valueObj->value}}"
                        class="custom-control-input filterAjax"
                        {{ in_array($valueObj->value, $selectedValues) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="{{$filter->filter_name}}{{$key}}">
                          {{ucfirst($valueObj->value)}}
                        </label>
                     </div>
                    @endforeach
                  </div>
                </div>
                @endforeach
                <!-- Dynamic Filters End -->
</div>