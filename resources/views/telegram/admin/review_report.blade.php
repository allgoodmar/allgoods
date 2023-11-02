<b>{{ setting('site.title') }} - Жалоба на отзыв</b>
ID отзыва: {{ $review->id }}
Сообщение: {{ $message }}
<a href="{{ route('voyager.reviews.edit', [$review->id]) }}">Редактировать</a>
