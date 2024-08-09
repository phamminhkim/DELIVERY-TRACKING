import sys
import pymupdf
import json

def find_text_position(pdf_path, src_page_num, search_text, src_index):
    doc = pymupdf.open(pdf_path)
    # Vị trí page tính từ 0
    page_num = src_page_num - 1
    page = doc[int(page_num)]
    text_instances = page.search_for(search_text)
    # Ví trí detect tính từ 0
    index = src_index - 1
    if int(index) < 0 or int(index) >= len(text_instances):
        return json.dumps({"error": "Index out of bounds"})

    page_height = int(page.rect.height)
    page_width = int(page.rect.width)

    if not page.is_wrapped:
        x_top_left = int(text_instances[index].x0)
        y_top_left = int(page_height - text_instances[index].y0)
        x_bottom_right = int(text_instances[index].x1)
        y_bottom_right = int(page_height - text_instances[index].y1)
    else:
        x_top_left = int(page_width - text_instances[index].y1)
        y_top_left = int(page_height - text_instances[index].x0)
        x_bottom_right = int(page_width - text_instances[index].y0)
        y_bottom_right = int(page_height - text_instances[index].x1)
    return json.dumps({"x0": x_top_left, "y0": y_top_left, "x1": x_bottom_right, "y1": y_bottom_right})

if __name__ == "__main__":
    # Kiểm tra xem có đủ đối số hay không
    if len(sys.argv) < 9:
        print(json.dumps({"error": "Usage: python find_text_position.py --pdf_path <file_path> --page_num <page_num> --search_text <search_text> --index <index>"}))
        sys.exit(1)

    # Tạo từ điển để lưu trữ các cặp key-value
    args_dict = {}
    # Duyệt qua các đối số và lưu trữ vào từ điển
    for i in range(1, len(sys.argv), 2):
        key = sys.argv[i]
        value = sys.argv[i + 1]
        args_dict[key] = value

    pdf_path = args_dict.get('--pdf_path')
    page_num = int(args_dict.get('--page_num')) # Truyền vào tính từ 1
    search_text = args_dict.get('--search_text')
    index = int(args_dict.get('--index')) # Truyền vào tính từ 1
    # Kiểm tra pdf trống thì in lỗi
    if not pdf_path:
        print(json.dumps({"error": "Missing pdf_path"}))
        sys.exit(1)
    # Kiểm tra page_num trống thì in lỗi
    if not page_num:
        print(json.dumps({"error": "Missing page_num"}))
        sys.exit(1)
    # Kiểm tra search_text trống thì in lỗi
    if not search_text:
        print(json.dumps({"error": "Missing search_text"}))
        sys.exit(1)
    # Kiểm tra index trống thì in lỗi
    if not index:
        print(json.dumps({"error": "Missing index"}))
        sys.exit(1)
    print(find_text_position(pdf_path, page_num, search_text, index))
