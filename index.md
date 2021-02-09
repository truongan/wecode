# Wecode online jduge

Là một hệ hống chấm bài tự động của các môn học lập trình được phát triển và sử dụng tại [trường Đại học Công nghệ Thông tin - Đại học Quốc gia  thành phố Hồ Chí Minh](uit.edu.vn). Cũng như các hệ thống online judge nói chung, wecode judge có thể tiến hành nhận các bài làm của sinh viên ở dạng mã nguồn, sau đó tiến hành biên dịch, thực thi và kiểm tra tính đúng đắn của bài làm với các bộ test do người ra đề quy định trước.

Hệ thống wecode được định hướng để đáp ứng các nhu cầu giảng dạy và học tập lập trình trong môi trường đại học như: giúp giảng viên giao bài tập và thu bài làm định kỳ, cho phép sinh viên tự luyện tập thêm, báo kết quả và tổng hợp bảng điểm ngay tức thời, v.v... Ngoài ra wecode judge cũng phù hợp tổ chức các cuộc thi lập trình theo format của IOI hoặc ICPC. 

## Các chức năng chính

Các chức năng chính có thể kể đến của wecode judge goomf:
- Sử dụng công nghệ container tiên tiến (docker) để xử lý bài làm của sinh viên. Cho phép bảo mật ở mức vừa cũng như cho phép giảng viên linh hoạt trong triển khai, thay đổi, cập nhật các môi trường lập trình chuẩn được quy ước với sinh viên.

- Cơ chế phân quyền đa dạng giữa sinh viên, trợ giảng, giảng viên đứng lớp, quản trị viên của hệ thống.

- Gửi bài làm của sinh viên lên hệ thống kiểm tra mã nguồn giống nhau của Stanford (MOSS) để phát hiện bài gian lận.

- Hỗ trợ đề bài dạng "điền vào chỗ trống" theo template có sẵn và hạn chế các từ khóa được sử dụng. Cho phép giảng viên định hướng đê bài nhắm vào các kỹ năng đặc thù riêng của ngôn ngữ lập trình

- Có thể chấm bài theo dạng so khớp test case hoặc chấm theo chương trình chấm riêng do giảng viên cung cấp

- Quản lý, chia sẻ đề bài chung giữa nhiều giảng viên, nhiều lớp, cho phép sinh viên có thể tự luyện tập thêm bên cạnh các bài tập tính giờ giao.

## Cài đặt và triển khai 

- Wecode judge được phát triển với framework PHP Laravel và có chung [yêu cầu server với laravel](https://laravel.com/docs/8.x/deployment#server-requirements)

- Server cần đặt Docker Community edition, và user sẽ chạy laravel có quyền sử dụng docker.

- Hệ quản trị cơ sở dữ liệu MySQL hoặc PostgreSQL 

Your Pages site will use the layout and styles from the Jekyll theme you have selected in your [repository settings](https://github.com/truongan/wecode/settings). The name of this theme is saved in the Jekyll `_config.yml` configuration file.

## Acknowledgement

- Nghiên cứu được tài trợ bởi Trường Đại học Công nghệ Thông tin – ĐHQG-HCM trong khuôn khổ Đề tài mã số D3-2019-01

- This research is funded by University of Information Technology-Vietnam National University HoChiMinh City under grant number D3-2019-01
