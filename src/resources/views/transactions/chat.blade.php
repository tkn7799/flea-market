@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
@php
    // 自分（ログインユーザー）の評価
    $myRating = $purchase->ratings()->where('from_user_id', auth()->id())->first();
    // 相手の評価
    $opponentRating = $purchase->ratings()->where('from_user_id', '!=', auth()->id())->first();

    $isBuyer = auth()->id() === $purchase->buyer_id;
    $isSeller = auth()->id() === $purchase->seller_id;
@endphp
<div class="chat-container">
    <h1 class="visually-hidden">取引メッセージ画面</h1>
    <aside class="side-menu">
        <h2>その他の取引</h2>
        <div class="other-transactions">
            @foreach($otherPurchases as $other)
                <a href="{{ route('transaction.chat', $other->id) }}" class="transaction-item">
                    {{ $other->product->product_name }}
                </a>
            @endforeach
        </div>
    </aside>

    <main class="chat-main">
        <header class="chat-header">
            <div class="header-left">
                <div class="user-info">
                    <div class="user-icon">
                        @if($purchase->buyer_id === auth()->id() && $purchase->seller->profile_image)
                            <img src="{{ asset('storage/' . $purchase->seller->profile_image) }}" alt="">
                        @elseif($purchase->seller_id === auth()->id() && $purchase->buyer->profile_image)
                            <img src="{{ asset('storage/' . $purchase->buyer->profile_image) }}" alt="">
                        @else
                            <div class="user-icon-placeholder"></div>
                        @endif
                    </div>

                    <h2>「{{ $purchase->buyer_id === auth()->id() ? $purchase->seller->user_name : $purchase->buyer->user_name }}」さんとの取引画面</h2>
                </div>
                <section class="transaction-status-message">
                    @if($purchase->status === 'completed')
                        <div class="status-alert status-alert--success">
                            取引が完了しました。ご利用ありがとうございました！
                        </div>
                    @elseif($myRating && !$opponentRating)
                        <div class="status-alert status-alert--info">
                            評価を送信しました。相手からの評価待ちです。
                        </div>
                    @elseif(!$myRating && $opponentRating)
                        <div class="status-alert status-alert--warning">
                            相手から評価されました。評価を返して取引を完了させてください。
                        </div>
                    @endif
                </section>
            </div>

            @if($purchase->status !== 'completed' && $isBuyer && !$myRating)
                <button type="button" class="btn-complete" onclick="showRatingModal()">取引を完了する</button>
            @endif
        </header>

        <section class="product-info-bar">
            <div class="product-img">
                @if($purchase->product->images && $purchase->product->images->first())
                    <img src="{{ asset('storage/' . $purchase->product->images->first()->image_path) }}" alt="{{ $purchase->product->product_name }}">
                @else
                    <div class="product-img-placeholder">No Image</div>
                @endif
            </div>

            <div class="product-details">
                <h3>{{ $purchase->product->product_name }}</h3>
                <p>¥{{ number_format($purchase->product->price) }}</p>
            </div>
        </section>

        <section class="message-section">
            <h2 class="visually-hidden">メッセージ履歴</h2>
            <div class="message-list">
                @foreach($messages as $message)
                    @php $isMine = $message->user_id === auth()->id(); @endphp

                    <div class="message-item {{ $isMine ? 'my-message' : 'other-message' }}">
                        <div class="message-user-row">
                            <span class="user-name">{{ $message->user->user_name }}</span>
                            <div class="user-icon-small">
                                @if($message->user->profile_image)
                                    <img src="{{ asset('storage/' . $message->user->profile_image) }}" alt="">
                                @else
                                    <div class="user-icon-placeholder"></div>
                                @endif
                            </div>
                        </div>

                        <div class="message-body">
                            <div class="message-content" id="content-{{ $message->id }}">
                                    <p class="text-display">{{ $message->message }}</p>

                                    @if($isMine)
                                        <form action="{{ route('transaction.message.update', [$purchase->id, $message->id]) }}" method="POST" class="edit-form" id="edit-form-{{ $message->id }}" style="display: none;">
                                            @csrf @method('PATCH')
                                            <input type="text" name="message" value="{{ $message->message }}" class="edit-input" required>
                                            <div class="edit-buttons">
                                                <button type="submit" class="save-btn">保存</button>
                                                <button type="button" class="cancel-btn" onclick="toggleEdit({{ $message->id }})">キャンセル</button>
                                            </div>
                                        </form>
                                    @endif

                                    @if($message->image_path)
                                        <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像" class="attached-image">
                                    @endif
                            </div>

                            @if($isMine)
                                <div class="message-actions">
                                    <a href="javascript:void(0)" onclick="toggleEdit({{ $message->id }})" class="edit-link">編集</a>
                                    <form action="{{ route('transaction.message.destroy', [$purchase->id, $message->id]) }}" method="POST" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="delete-btn">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <footer class="chat-footer">
            <div id="image-preview-container" style="display: none;">
                <img id="image-preview" src="" alt="プレビュー">
                <button type="button" id="remove-preview">×</button>
            </div>

            @error('message')
                <div class="error-text">{{ $message }}</div>
            @enderror

            @error('image')
                <div class="error-text">{{ $message }}</div>
            @enderror
            <form action="{{ route('transaction.message.store', $purchase->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" placeholder="取引メッセージを記入してください" value="{{ old('message') }}">
                    <label class="btn-image">
                        画像を追加
                        <input type="file" name="image" id="image-input" class="visually-hidden">
                    </label>
                    <button type="submit" class="btn-send">
                        <img src="{{ asset('storage/inputbuttun.png') }}" alt="送信">
                    </button>
                </div>
            </form>
        </footer>
    </main>
</div>

<div id="rating-modal" class="modal-overlay">
    <div class="rating-modal">
        <div class="rating-modal__header">
            <h2>取引が完了しました。</h2>
        </div>

        <div class="rating-modal__body">
            <p class="rating-modal__label">今回の取引相手はどうでしたか？</p>
            <form action="{{ route('rating.store', $purchase->id) }}" method="POST">
                @csrf
                <div class="star-rating">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required class="visually-hidden">
                        <label for="star{{ $i }}">★</label>
                    @endfor
                </div>

                <div class="rating-modal__footer">
                    <button type="submit" class="rating-submit-btn">送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleEdit(messageId) {
        const contentDiv = document.getElementById(`content-${messageId}`);
        const textDisplay = contentDiv.querySelector('.text-display');
        const editForm = document.getElementById(`edit-form-${messageId}`);

        if (editForm.style.display === 'none') {
            textDisplay.style.display = 'none';
            editForm.style.display = 'block';
        } else {
            textDisplay.style.display = 'block';
            editForm.style.display = 'none';
        }
    }

    function showRatingModal() {
        const modal = document.getElementById('rating-modal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    // モーダルの外側をクリックしたら閉じる（任意）
    window.onclick = function(event) {
        const modal = document.getElementById('rating-modal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($isSeller && $opponentRating && !$myRating)
            showRatingModal();
        @endif

        const messageInput = document.querySelector('input[name="message"]');
        if (messageInput) {
            const chatForm = messageInput.closest('form');
            const storageKey = `chat_draft_{{ $purchase->id }}`;

        // 1. ページ読み込み時に保存された内容を復元
            const savedMessage = localStorage.getItem(storageKey);
            if (savedMessage) {
                messageInput.value = savedMessage;
            }

        // 2. 入力するたびに localStorage に保存
            messageInput.addEventListener('input', function() {
                localStorage.setItem(storageKey, messageInput.value);
            });

        // 3. 送信が完了したら localStorage を空にする
            chatForm.addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image-input');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('image-preview');
        const removePreview = document.getElementById('remove-preview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        removePreview.addEventListener('click', function() {
            imageInput.value = "";
            previewContainer.style.display = 'none';
            previewImage.src = "";
        });

        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', function() {
                setTimeout(() => {
                    previewContainer.style.display = 'none';
                }, 100);
            });
        }
    });

</script>
@endsection