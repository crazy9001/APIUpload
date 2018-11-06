@extends('layout')

@section('page-title', 'DropZone')

@section('body-end')
    <script src="{{ asset('vendor/dropzone/dropzone.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}"/>
    <script>
        Dropzone.autoDiscover = false;
    </script>
@endsection

@section('content')
    <h2>Example</h2>
    <div class="text-center">

        <form action="{{ url('upload') }}"
              class="dropzone"
              id="my-awesome-dropzone">
            <input type="file" name="file" style="display: none;">
        </form>
        <small>Works only in Chrome</small>
        <ul id="file-upload-list" class="list-unstyled">

        </ul>
    </div>

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#__mediaUploadFile">Media Manager</button>

    <div class="modal fade" id="__mediaUploadFile" role="dialog" aria-hidden="true">
        <div class="media-modal wp-core-ui">
            <button type="button" class="button-link media-modal-close">
        <span class="media-modal-icon">
            <span class="screen-reader-text">Đóng bảng điều khiển Media</span>
        </span>
            </button>
            <div class="media-modal-content">
                <div class="media-frame mode-select wp-core-ui" id="__wp-uploader-id-0">
                    <div class="media-frame-menu">
                        <div class="media-menu">
                            <a href="#" class="media-menu-item active"> Danh sách ảnh </a>
                            <div class="separator"></div>
                            <a href="#" class="media-menu-item">Chèn từ URL</a></div>
                    </div>
                    <div class="media-frame-title">
                        <h1>Chèn nội dung đa phương tiện
                            <span class="dashicons dashicons-arrow-down"></span>
                        </h1>
                    </div>
                    <div class="media-frame-router">
                        <div class="media-router">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab" href="#media-uploader-inline" class="media-menu-item" >Tải tập tin lên</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#media-attachments-browser" class="media-menu-item" >Thư viện nội dung đa phương tiện</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="media-frame-content tab-content" data-columns="9">

                        <div class="uploader-inline tab-pane fade in active" id="media-uploader-inline">
                            <div class="uploader-inline-content no-upload-message">
                                <div class="upload-ui">
                                    <form action="{{ url('upload') }}"
                                          class="dropzone"
                                          id="mediaZoneUpload">
                                        <input type="file" name="file" style="display: none;">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="attachments-browser tab-pane fade" id="media-attachments-browser">
                            <div class="media-toolbar">
                                <div class="media-toolbar-secondary">
                                    <label for="media-attachment-filters" class="screen-reader-text"> Lọc theo loại</label>
                                    <select id="media-attachment-filters" class="attachment-filters">
                                        <option value="all">Tất cả</option>
                                        <option value="uploaded">Uploaded to this product</option>
                                        <option value="image">Hình ảnh</option>
                                        <option value="audio">Âm thanh</option>
                                        <option value="video">Video</option>
                                        <option value="unattached">Chưa được đính kèm</option>
                                    </select>
                                    <label for="media-attachment-date-filters" class="screen-reader-text">Lọc theo ngày</label>
                                    <select id="media-attachment-date-filters" class="attachment-filters">
                                        <option value="all">Tất cả các ngày</option>
                                        <option value="0">Tháng Mười 2018</option>
                                        <option value="1">Tháng Chín 2016</option>
                                    </select>
                                    <span class="spinner"></span>
                                </div>
                                <div class="media-toolbar-primary search-form">
                                    <label for="media-search-input" class="screen-reader-text"> Tìm trong đa phương tiện </label>
                                    <input type="search" placeholder="Tìm kiếm" id="media-search-input" class="search">
                                </div>
                            </div>
                            <div class="uploader-inline hidden">
                                <div class="uploader-inline-content has-upload-message">
                                    <h2 class="upload-message">Không có mục nào được tìm thấy.</h2>
                                    <div class="upload-ui">
                                        <h2 class="upload-instructions drop-instructions">
                                            Kéo thả các các file vào bất kì nơi nào trên trang này để tải lên.
                                        </h2>
                                        <p class="upload-instructions drop-instructions">hoặc</p>
                                        <a href="#" class="browser button button-hero" style="display: inline; position: relative; z-index: 1;" id="__wp-uploader-id-1">
                                            Chọn tập tin
                                        </a>
                                    </div>
                                    <div class="upload-inline-status"></div>
                                    <div class="post-upload-ui">
                                        <p class="max-upload-size">Kích thước tập tin tải lên tối đa: 128 MB</p>
                                    </div>
                                </div>
                            </div>
                            <ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-720">
                                <li tabindex="0" role="checkbox"
                                    aria-label="Ít Nhưng Dài Lâu (Remix Version 2) - Yan Nguyễn [Audio Official]"
                                    aria-checked="false" data-id="233" class="attachment save-ready">
                                    <div class="attachment-preview type-video subtype-mp4 landscape">
                                        <div class="thumbnail">
                                            <div class="centered">
                                                <img src="http://dienmaycuonglinh.com/wp-includes/images/media/video.png" class="icon" draggable="false" alt="">
                                            </div>
                                            <div class="filename">
                                                <div>Ít-Nhưng-Dài-Lâu-Remix-Version-2-Yan-Nguyễn-Audio-Official-1.mp4</div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="button-link check" tabindex="0">
                                        <span class="media-modal-icon"></span>
                                        <span class="screen-reader-text">Bỏ chọn</span>
                                    </button>
                                </li>
                                <li tabindex="0" role="checkbox" aria-label="logo cuonglinh1" aria-checked="false" data-id="226"
                                    class="attachment save-ready">
                                    <div class="attachment-preview js&#45;&#45;select-attachment type-image subtype-jpeg landscape">
                                        <div class="thumbnail">
                                            <div class="centered">
                                                <img src="https://images.pexels.com/photos/417040/pexels-photo-417040.jpeg"
                                                     draggable="false" alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="button-link check" tabindex="-1">
                                        <span class="media-modal-icon"></span>
                                        <span class="screen-reader-text">Bỏ chọn</span>
                                    </button>
                                </li>
                            </ul>
                            <div class="media-sidebar">

                                <div class="media-uploader-status" style="display: none;">
                                    <h2>Đang tải lên</h2>
                                    <button type="button" class="button-link upload-dismiss-errors">
                                        <span class="screen-reader-text">Bỏ qua lỗi</span>
                                    </button>
                                    <div class="media-progress-bar">
                                        <div></div>
                                    </div>
                                    <div class="upload-details">
                                    <span class="upload-count">
                                        <span class="upload-index"></span> / <span class="upload-total"></span>
                                    </span>
                                        <span class="upload-detail-separator">–</span>
                                        <span class="upload-filename"></span>
                                    </div>
                                    <div class="upload-errors"></div>
                                </div>
                                <!--<div tabindex="0" data-id="184" class="attachment-details save-ready">
                                    <h2>
                                        Chi tiết đính kèm <span class="settings-save-status">
                                            <span class="spinner"></span>
                                            <span class="saved">Đã lưu.</span>
                                        </span>
                                    </h2>
                                    <div class="attachment-info">
                                        <div class="thumbnail thumbnail-image">
                                            <img src="http://dienmaycuonglinh.com/wp-content/uploads/2018/10/Navi-P-rau-nhút-Thái.jpg"
                                                 draggable="false" alt="">
                                        </div>
                                        <div class="details">
                                            <div class="filename">Navi-P-rau-nhút-Thái.jpg</div>
                                            <div class="uploaded">11 Tháng Mười, 2018</div>
                                            <div class="file-size">209 KB</div>
                                            <div class="dimensions">960 × 1280</div>
                                            <a class="edit-attachment"
                                               href="http://dienmaycuonglinh.com/wp-admin/post.php?post=184&amp;action=edit&amp;image-editor"
                                               target="_blank">Sửa ảnh</a>
                                            <button type="button" class="button-link delete-attachment">Xóa vĩnh viễn
                                            </button>
                                            <div class="compat-meta">
                                            </div>
                                        </div>
                                    </div>
                                    <label class="setting" data-setting="url">
                                        <span class="name">URL</span>
                                        <input type="text"
                                               value="http://dienmaycuonglinh.com/wp-content/uploads/2018/10/Navi-P-rau-nhút-Thái.jpg"
                                               readonly="">
                                    </label>
                                    <label class="setting" data-setting="title">
                                        <span class="name">Tiêu đề</span>
                                        <input type="text" value="Navi P rau nhút Thái">
                                    </label>
                                    <label class="setting" data-setting="caption">
                                        <span class="name">Chú thích</span>
                                        <textarea></textarea>
                                    </label>
                                    <label class="setting" data-setting="alt">
                                        <span class="name">Văn bản thay thế</span>
                                        <input type="text" value="">
                                    </label>
                                    <label class="setting" data-setting="description">
                                        <span class="name">Mô tả</span>
                                        <textarea></textarea>
                                    </label>
                                </div>

                                <div class="attachment-display-settings">
                                    <h2>Tùy chọn hiển thị nội dung đính kèm</h2>
                                    <label class="setting">
                                        <span>Căn chỉnh</span>
                                        <select class="alignment" data-setting="align" data-user-setting="align">

                                            <option value="left">
                                                Trái
                                            </option>
                                            <option value="center">
                                                Chính giữa
                                            </option>
                                            <option value="right">
                                                Phải
                                            </option>
                                            <option value="none" selected="">
                                                Trống
                                            </option>
                                        </select>
                                    </label>
                                    <div class="setting">
                                        <label>
                                            <span>Liên kết tới</span>
                                            <select class="link-to" data-setting="link" data-user-setting="urlbutton">
                                                <option value="none" selected=""> Trống </option>
                                                <option value="file"> Tập tin đa phương tiện </option>
                                                <option value="post"> Trang nội dung đính kèm </option>
                                                <option value="custom"> URL tùy chỉnh </option>
                                            </select>
                                        </label>
                                        <input type="text" class="link-to-custom hidden" data-setting="linkUrl">
                                    </div>
                                    <label class="setting">
                                        <span>Kích cỡ</span>
                                        <select class="size" name="size" data-setting="size" data-user-setting="imgsize">
                                            <option value="full" selected="selected">
                                                Kích thước đầy đủ – 960 × 1280
                                            </option>
                                        </select>
                                    </label>
                                </div>-->

                            </div>
                        </div>

                    </div>
                    <div class="media-frame-toolbar">
                        <div class="media-toolbar">
                            <div class="media-toolbar-secondary">
                                <div class="media-selection empty">
                                    <div class="selection-info">
                                        <span class="count">0 được chọn</span>
                                        <button type="button" class="button-link edit-selection">Sửa lựa chọn</button>
                                        <button type="button" class="button-link clear-selection">Xóa</button>
                                    </div>
                                    <div class="selection-view">
                                        <ul tabindex="-1" class="attachments" id="__attachments-view-111"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="media-toolbar-primary search-form">
                                <button type="button"
                                        class="button media-button button-primary button-large media-button-insert"
                                        disabled="disabled">Chèn ảnh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection