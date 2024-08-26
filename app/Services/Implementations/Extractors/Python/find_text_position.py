import sys
import pymupdf
import json

def find_text_position(pdf_path, src_page_num, search_text, src_index):
    try:
        doc = pymupdf.open(pdf_path)
        # Lấy số page của doc
        num_pages = len(doc)
        # Kiểm tra nếu vị trí page truyền vào không lớn hơn num_pages
        if src_page_num > num_pages:
            return json.dumps({"error": "Page out of bounds"})
        # Vị trí page tính từ 0
        page_num = src_page_num - 1
        page = doc[int(page_num)]
        rotation = page.rotation  # Kiểm tra xoay trang
        text_instances = page.search_for(search_text)
        if not text_instances:
            return json.dumps({"error": "Text not found"})

        # Số chuỗi được tìm thấy
        num_found_text = len(text_instances)
        # Ví trí detect tính từ 0
        index = src_index - 1
        if int(index) < 0 or int(index) >= len(text_instances):
            return json.dumps({"error": "Index out of bounds"})
        page_height = int(page.rect.height)
        page_width = int(page.rect.width)
        if rotation == 0:
            text_instances = [pymupdf.Rect(r.x0, page_height - r.y0, r.x1, page_height - r.y1) for r in text_instances]
        if rotation == 90:
            text_instances = [pymupdf.Rect(page_width - r.y1, page_height - r.x0, page_width - r.y0, page_height - r.x1) for r in text_instances]
        # elif rotation == 180:
        #     text_instances = [pymupdf.Rect(page_width - r.x1, page_height - r.y1, page_width - r.x0, page_height - r.y0) for r in text_instances]
        # elif rotation == 270:
        #     text_instances = [pymupdf.Rect(r.y0, page_height - r.x1, r.y1, page_height - r.x0) for r in text_instances]

        x_top_left = int(text_instances[index].x0)
        y_top_left = int(text_instances[index].y0)
        x_bottom_right = int(text_instances[index].x1)
        y_bottom_right = int(text_instances[index].y1)
        return json.dumps(
            {
                "rect_coord":
                    {
                        "x0": x_top_left,
                        "y0": y_top_left,
                        "x1": x_bottom_right,
                        "y1": y_bottom_right
                    },
                "num_found_text": num_found_text,
                "page_rotation": rotation
            })
    except Exception as e:
        return json.dumps({"error": str(e)})

def get_text_by_coords(pdf_path, src_page_num, coords):
    try:
        doc = pymupdf.open(pdf_path)
        # Lấy số page của doc
        num_pages = len(doc)
        # Kiểm tra nếu vị trí page truyền vào không lớn hơn num_pages
        if src_page_num > num_pages:
            return json.dumps({"error": "Page out of bounds"})
        # Vị trí page tính từ 0
        page_num = src_page_num - 1
        page = doc[int(page_num)]
        rotation = page.rotation  # Kiểm tra xoay trang
        # Lấy tọa độ nguồn
        coords = coords.split(' ')
        src_x0 = int(coords[0])
        src_y0 = int(coords[1])
        src_x1 = int(coords[2])
        src_y1 = int(coords[3])

        # Quy tọa độ đích
        page_height = int(page.rect.height)
        page_width = int(page.rect.width)
        if rotation == 0:
            x0 = int(src_x0)
            y0 = int(page_height - src_y0)
            x1 = int(src_x1)
            y1 = int(page_height - src_y1)
        if rotation == 90:
            x0 = int(page_height - src_y0)
            y0 = int(page_width - src_x1)
            x1 = int(page_height - src_y1)
            y1 = int(page_width - src_x0)

        rect = pymupdf.Rect(x0, y0, x1, y1)
        # Trích xuất văn bản từ vùng chữ nhật
        text_instances = page.get_textbox(rect)

        if not text_instances:
            return json.dumps({"error": "Not found text"})
        return json.dumps({"text": text_instances})
    except Exception as e:
        return json.dumps({"error": str(e)})

def get_full_text(pdf_path, src_page_num, output_path):

    try:
        merged_data = []
        doc = pymupdf.open(pdf_path)
        # Vị trí page tính từ 0
        page_num = src_page_num - 1
        page = doc[int(page_num)]
        full_text = page.get_text()
        # with open(output_path, 'wb') as f:
        #     f.write(full_text)
        with open(output_path, 'wb') as f:
            f.write(full_text.encode('utf-8'))

        return json.dumps({"result": True})
    except Exception as e:
        return json.dumps({"error": str(e)})

def check_string_key(pdf_path, src_page_num, string_key):
    try:
        doc = pymupdf.open(pdf_path)
        # Vị trí page tính từ 0
        page_num = src_page_num - 1
        page = doc[int(page_num)]
        full_text = page.get_text()
        full_text = json.dumps(full_text)
        # Kiểm tra string_key có tìm thấy trong full text
        if string_key in full_text:
            return json.dumps({"is_exist": True})
        else:
            return json.dumps({"is_exist": False})
    except Exception as e:
        return json.dumps({"error": str(e)})

if __name__ == "__main__":
    # Tạo từ điển để lưu trữ các cặp key-value
    args_dict = {}
    # Duyệt qua các đối số và lưu trữ vào từ điển
    for i in range(1, len(sys.argv), 2):
        key = sys.argv[i]
        value = sys.argv[i + 1]
        args_dict[key] = value

    pdf_path = args_dict.get('--pdf_path')
    page_num = int(args_dict.get('--page_num')) # Truyền vào tính từ 1
    method = args_dict.get('--method')
    if not pdf_path:
        print(json.dumps({"error": "Missing pdf_path"}))
        sys.exit(1)
    # Kiểm tra page_num trống thì in lỗi
    if not page_num:
        print(json.dumps({"error": "Missing page_num"}))
        sys.exit(1)
    # Kiểm tra method trống thì in lỗi
    if not method:
        print(json.dumps({"error": "Missing method"}))
        sys.exit(1)

    # Lấy tọa độ theo text
    if method == 'get_coords_by_text':
        search_text = args_dict.get('--search_text')
        index = int(args_dict.get('--index')) # Truyền vào tính từ 1
        # Kiểm tra search_text trống thì in lỗi
        if not search_text:
            print(json.dumps({"error": "Missing search_text"}))
        # Kiểm tra index trống thì in lỗi
        elif not index:
            print(json.dumps({"error": "Missing index"}))
        else:
            print(find_text_position(pdf_path, page_num, search_text, index))

    # Lấy text theo tọa độ
    elif method == 'get_text_by_coords':
        coords = args_dict.get('--coords')
        # Kiểm tra coords trống thì in lỗi
        if not coords:
            print(json.dumps({"error": "Missing coords"}))
        else:
            print(get_text_by_coords(pdf_path, page_num, coords))

    # Lấy full text của page
    elif method == 'get_full_text':
        output_path = args_dict.get('--output_path')
        if not output_path:
            print(json.dumps({"error": "Missing output_path"}))
        else:
            print(get_full_text(pdf_path, page_num, output_path))

    # Check string_key trên page
    elif method == 'check_string_key':
        string_key = args_dict.get('--string_key')
        # Kiểm tra string_key trống thì in lỗi
        if not string_key:
            print(json.dumps({"error": "Missing string_key"}))
        else:
            print(check_string_key(pdf_path, page_num, string_key))

    else:
        print(json.dumps({"error": "Invalid method"}))

    sys.exit(1)
