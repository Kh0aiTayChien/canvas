<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Drawing</title>
    <style>
        #canvas {
            border: 1px solid black;
        }
        button {
            margin-top: 10px;
        }
        .gallery {
            margin-top: 20px;
        }
        .thumbnail {
            display: inline-block;
            margin: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            position: relative;
        }
        .thumbnail img {
            max-width: 100px;
            max-height: 75px;
        }
        .delete-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 0 5px;
        }
        .upload-container {
            margin-top: 20px;
        }
        .upload-container input[type="file"] {
            display: none;
        }
        .upload-container label {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
        }
        .controls {
            margin-top: 20px;
        }
        .controls button, .controls input[type="color"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<h1>Canvas Drawing</h1>
<canvas id="canvas" width="800" height="600"></canvas>

<div class="controls">
    <button id="drawLine">Line</button>
    <button id="drawRect">Rectangle</button>
    <button id="drawCircle">Circle</button>
    <input type="color" id="colorPicker" value="#000000">
    <button id="saveButton">Save Drawing</button>
    <input type="hidden" id="currentImageId" value="">
</div>

<div class="upload-container">
    <label for="fileInput">Upload Image</label>
    <input type="file" id="fileInput" accept="image/*">
</div>

<div class="gallery">
    <h2>Saved Images</h2>
    @foreach($images as $image)
        <div class="thumbnail" data-image="{{ $image->image }}" data-id="{{ $image->id }}">
            <img src="{{ $image->image }}" alt="Saved Image">
            <button class="delete-btn" data-id="{{ $image->id }}">X</button>
        </div>
    @endforeach
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const canvas = $('#canvas')[0];
        const ctx = canvas.getContext('2d');
        let painting = false;
        let currentTool = 'pen'; // Công cụ vẽ hiện tại
        let currentColor = '#000000'; // Màu sắc hiện tại
        let shapes = []; // Lưu trữ tất cả các hình đã vẽ
        let currentShape = null; // Hình hiện tại đang vẽ
        let startX, startY; // Vị trí bắt đầu vẽ hình

        function startPosition(e) {
            painting = true;
            startX = e.clientX - canvas.offsetLeft;
            startY = e.clientY - canvas.offsetTop;

            if (currentTool !== 'pen') {
                currentShape = { tool: currentTool, startX, startY, color: currentColor };
            } else {
                ctx.beginPath();
                ctx.moveTo(startX, startY);
            }
        }

        function endPosition() {
            if (currentTool !== 'pen' && currentShape) {
                // Tính toán các thông số của hình vẽ
                const endX = event.clientX - canvas.offsetLeft;
                const endY = event.clientY - canvas.offsetTop;

                if (currentShape.tool === 'line') {
                    currentShape.endX = endX;
                    currentShape.endY = endY;
                } else if (currentShape.tool === 'rect') {
                    currentShape.width = endX - currentShape.startX;
                    currentShape.height = endY - currentShape.startY;
                } else if (currentShape.tool === 'circle') {
                    currentShape.endX = endX;
                    currentShape.endY = endY;
                    currentShape.radius = Math.sqrt(Math.pow(endX - currentShape.startX, 2) + Math.pow(endY - currentShape.startY, 2));
                }

                shapes.push(currentShape); // Lưu hình vào danh sách shapes
                currentShape = null;
            }

            painting = false;
            ctx.beginPath();
            redraw(); // Vẽ lại tất cả các hình đã lưu
        }

        function draw(e) {
            if (!painting) return;

            if (currentTool === 'pen') {
                ctx.lineWidth = 5;
                ctx.lineCap = 'round';
                ctx.strokeStyle = currentColor;

                ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
            } else if (currentShape) {
                ctx.clearRect(0, 0, canvas.width, canvas.height); // Xóa canvas trước khi vẽ hình mới
                redraw(); // Vẽ lại tất cả các hình đã lưu

                const x = e.clientX - canvas.offsetLeft;
                const y = e.clientY - canvas.offsetTop;

                ctx.strokeStyle = currentShape.color;
                ctx.lineWidth = 2;

                if (currentShape.tool === 'line') {
                    ctx.beginPath();
                    ctx.moveTo(currentShape.startX, currentShape.startY);
                    ctx.lineTo(x, y);
                    ctx.stroke();
                } else if (currentShape.tool === 'rect') {
                    ctx.beginPath();
                    ctx.rect(currentShape.startX, currentShape.startY, x - currentShape.startX, y - currentShape.startY);
                    ctx.stroke();
                } else if (currentShape.tool === 'circle') {
                    const radius = Math.sqrt(Math.pow(x - currentShape.startX, 2) + Math.pow(y - currentShape.startY, 2));
                    ctx.beginPath();
                    ctx.arc(currentShape.startX, currentShape.startY, radius, 0, Math.PI * 2);
                    ctx.stroke();
                }
            }
        }

        function redraw() {
            shapes.forEach(shape => {
                ctx.strokeStyle = shape.color;
                ctx.lineWidth = 2;

                if (shape.tool === 'line') {
                    ctx.beginPath();
                    ctx.moveTo(shape.startX, shape.startY);
                    ctx.lineTo(shape.endX, shape.endY);
                    ctx.stroke();
                } else if (shape.tool === 'rect') {
                    ctx.beginPath();
                    ctx.rect(shape.startX, shape.startY, shape.width, shape.height);
                    ctx.stroke();
                } else if (shape.tool === 'circle') {
                    ctx.beginPath();
                    ctx.arc(shape.startX, shape.startY, shape.radius, 0, Math.PI * 2);
                    ctx.stroke();
                }
            });
        }

        // Chọn công cụ
        $('#drawLine').click(function() {
            currentTool = 'line';
        });

        $('#drawRect').click(function() {
            currentTool = 'rect';
        });

        $('#drawCircle').click(function() {
            currentTool = 'circle';
        });

        // Chọn màu sắc
        $('#colorPicker').change(function() {
            currentColor = $(this).val();
        });

        $(canvas).on('mousedown', startPosition);
        $(canvas).on('mouseup', endPosition);
        $(canvas).on('mousemove', draw);

        $('#saveButton').click(function() {
            const dataURL = canvas.toDataURL('image/png');
            const imageId = $('#currentImageId').val();
            const url = imageId ? `/canvas/update/${imageId}` : '/canvas/save';

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    image: dataURL,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Tải lại trang để cập nhật danh sách hình ảnh
                },
                error: function(xhr) {
                    alert('Failed to save or update image.');
                }
            });
        });

        // Load image on canvas
        $('.thumbnail').click(function() {
            const imageUrl = $(this).data('image');
            const imageId = $(this).data('id');
            $('#currentImageId').val(imageId);
            loadImageToCanvas(imageUrl);
        });

        // Delete image
        $('.delete-btn').click(function(event) {
            event.stopPropagation(); // Ngăn chặn sự kiện click của thumbnail
            const imageId = $(this).data('id');

            $.ajax({
                url: `/canvas/delete/${imageId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Tải lại trang để cập nhật danh sách hình ảnh
                },
                error: function(xhr) {
                    alert('Failed to delete image.');
                }
            });
        });

        // Upload image
        $('#fileInput').change(function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                loadImageToCanvas(e.target.result);
            };

            reader.readAsDataURL(file);
        });

        function loadImageToCanvas(url) {
            const img = new Image();
            img.crossOrigin = 'Anonymous'; // Giúp tránh lỗi khi tải hình ảnh từ nguồn khác
            img.src = url;

            img.onload = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height); // Xóa canvas trước khi vẽ ảnh mới
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                redraw(); // Vẽ lại tất cả các hình đã lưu
            };
        }
    });

</script>
</body>
</html>
