*{{ setting('site.title') }} - Контактная форма*
*Имя:* {{ $contact->name }}
*Телефон:* {{ $contact->phone }}
*Сообщение:* {{ $contact->message }}
*Форма:* {{ $contact->type_title }}
@if($product)
*Товар:* [{{ $product->name }}]({{ $product->url }})
@endif
@if($productVariant)
*Вариант товара:* {{ $productVariant->name }}
@endif
