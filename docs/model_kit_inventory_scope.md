# Model Kit Inventory Scope

Tai lieu nay cap nhat huong du an tu quan ly kho chung sang quan ly kho mo hinh nhua/model kit.

## Muc tieu

Ung dung quan ly ton kho cho shop hoac kho ban mo hinh lap rap nhu Gunpla, mecha model kit, dung cu va phu kien lam mo hinh. Backend Laravel tra API cho Vue, uu tien code tay de hoc ro luong nghiep vu.

## Catalog chinh

- Manufacturer: hang san xuat nhu Bandai Spirits, Motor Nuclear, GSI Creos.
- Series: dong/universe cua san pham nhu Mobile Suit Gundam, Gundam SEED, Motor Nuclear Originals.
- Product: model kit hoac phu kien, co them `kit_code`, `grade`, `scale`, `material`, `runner_count`, `release_date`, `box_art_url`.
- Product variant: SKU ban ra/nhap kho, co `edition`, `box_condition`, `item_condition`, `has_manual`, `has_decals`, gia nhap, gia ban va ton toi thieu.
- Product category: nhom hang nhu Gunpla, Mecha Model Kits, Dung cu va phu kien.

## Trang thai can quan tam

- `active`: dang ban/duoc nhap xuat binh thuong.
- `inactive`: tam an khoi luong thao tac.
- `discontinued`: ngung san xuat/ngung kinh doanh nhung van giu lich su ton kho.
- `pre_order`: hang dat truoc, co the quan ly nhu cau truoc khi co hang that.

## Luong hoat dong

1. Quan ly catalog: tao hang san xuat, series, danh muc, product va variant SKU.
2. Nhap hang: tao purchase order, nhan hang bang goods receipt, gan vi tri luu kho.
3. Ton kho: cap nhat stock level theo warehouse, location va variant SKU.
4. Ban/xuat hang: tao sales order, xuat so luong tu ton kha dung.
5. Chuyen kho: chuyen variant tu kho HCM sang kho HN hoac giua cac khu luu tru.
6. Kiem ke/dieu chinh: ghi nhan mat thieu, hop mop, thieu decal/manual, sau do tao stock movement.
7. Canh bao: theo doi low stock, preorder, chenh lech kiem ke va audit log.

## Chuc nang nen lam tiep

- CRUD Manufacturer va Series.
- CRUD Product Variant, gom loc theo tinh trang hop, edition, manual/decal.
- Man hinh ton kho theo SKU: xem on hand, reserved, reorder point, max stock.
- Nhap hang va phieu nhan hang.
- Xuat hang/ban hang.
- Chuyen kho.
- Kiem ke va dieu chinh ton.
- Bao cao: ton thap, SKU ban chay, hang preorder, hang bi loi/hop mop.

## Ghi chu thiet ke

Product luu thong tin chung cua model kit, con Product Variant dai dien cho SKU thuc te trong kho. Vi du cung mot kit co the co ban standard, limited edition, hop moi, hop mop, hang da mo kiem tra, hoac batch preorder.
