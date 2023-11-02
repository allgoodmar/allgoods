@extends('layouts.app')

@section('seo_title', __('main.compare_list'))
@section('meta_description', '')
@section('meta_keywords', '')

@section('content')

@include('partials.page_top', ['title' => __('main.compare_list')])

<div class="section pt-4">
	<div class="custom-container">

        @if($compareList)
            <div class="table-responsive">
                <table class="compare-table table table-bordered">
                    <tr>
                        <td class="compare-titles-td">&nbsp;</td>
                        @foreach ($compareList as $compareItemId => $compareListItem)
                            <td data-compare-id="{{ $compareItemId }}" class="compare-img-td">
                                <div class="compare-img-one">
                                    <a href="{{ $compareListItem['product']->url }}" class="d-block">
                                        <img src="{{ $compareListItem['product']->small_img }}" alt="{{ $compareListItem['product']->name }}" class="img-fluid">
                                    </a>
                                    <button type="button"
                                        class="remove-from-compare-btn sticker sticker-delete btn btn-outline-danger btn-xs btn-round only-icon"
                                        data-id="{{ $compareListItem['product']->id }}"
                                        data-name="{{ $compareListItem['product']->name }}"
                                        data-price="{{ $compareListItem['product']->current_price }}"
                                        data-add-url="{{ route('compare.add') }}"
                                        data-delete-url="{{ route('compare.delete', ['id' => $compareListItem['product']->id]) }}"
                                        title="{{ __('main.delete_from_compare') }}"
                                        data-add-text="{{ __('main.add_to_compare') }}"
                                        data-delete-text="{{ __('main.delete_from_compare') }}"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('main.title') }}</strong>
                        </td>
                        @foreach ($compareList as $compareItemId => $compareListItem)
                            <td data-compare-id="{{ $compareItemId }}">
                                <h6 class="mb-0">
                                    <a href="{{ $compareListItem['product']->url }}" class="black-link">{{ $compareListItem['product']->name }}</a>
                                </h6>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ __('main.price') }}</strong>
                        </td>
                        @foreach ($compareList as $compareItemId => $compareListItem)
                            <td data-compare-id="{{ $compareItemId }}">
                                {{ Helper::formatPrice($compareListItem['product']->current_price) }}
                            </td>
                        @endforeach
                    </tr>
                    @foreach ($allAttributes as $attributeID => $attributeName)
                        <tr>
                            <td>
                                <strong>{{ $attributeName }}</strong>
                            </td>
                            @foreach ($compareList as $compareItemId => $compareListItem)
                                <td data-compare-id="{{ $compareItemId }}">
                                    {{ implode(', ', $compareListItem['attributes'][$attributeID]) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div class="my-4 lead text-center">{{ __('main.compare_list_is_empty') }}</div>
        @endif


    </div>
</div>

@endsection
