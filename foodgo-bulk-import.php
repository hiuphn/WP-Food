<?php
require_once('wp-load.php');

// Danh sách 30 món ăn được tuyển chọn kỹ lưỡng
$foods = [
    // --- Món Âu/Á Hiện Đại ---
    ['name' => 'Pizza Hải Sản Premium', 'price' => 149000, 'desc' => 'Hải sản tươi sống hòa quyện cùng phô mai Mozzarella.', 'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800', 'cats' => ['main-dish', 'pizza']],
    ['name' => 'Burger Bò Mỹ Nướng', 'price' => 99000, 'desc' => 'Thịt bò Mỹ nướng lửa hồng, đậm đà hương vị BBQ.', 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800', 'cats' => ['main-dish', 'burger']],
    ['name' => 'Sushi Cá Hồi Tươi', 'price' => 189000, 'desc' => 'Cá hồi Na Uy tươi rói phục vụ cùng cơm dẻo Nhật.', 'image' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=800', 'cats' => ['main-dish']],
    ['name' => 'Mì Ramen Nhật Bản', 'price' => 129000, 'desc' => 'Nước dùng đậm đà ninh từ xương 12 tiếng.', 'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800', 'cats' => ['main-dish']],
    ['name' => 'Mì Ý Carbonara', 'price' => 105000, 'desc' => 'Sốt kem trứng béo ngậy và thịt xông khói giòn.', 'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800', 'cats' => ['main-dish']],
    ['name' => 'Bít Tết Bò Úc', 'price' => 299000, 'desc' => 'Thịt bò Úc mềm mọng dùng kèm sốt tiêu đen.', 'image' => 'https://images.unsplash.com/photo-1546241072-48010ad28c2c?w=800', 'cats' => ['main-dish']],
    ['name' => 'Salad Ức Gà Healthy', 'price' => 85000, 'desc' => 'Ức gà áp chảo cùng rau organic tươi mát.', 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800', 'cats' => ['healthy', 'appetizers-starters']],
    ['name' => 'Gà Rán Giòn Cay', 'price' => 75000, 'desc' => 'Lớp vỏ giòn tan, thịt bên trong mềm ngọt.', 'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=800', 'cats' => ['main-dish']],
    ['name' => 'Khoai Tây Chiên Sốt Bơ', 'price' => 45000, 'desc' => 'Khoai tây vàng ruộm lắc cùng sốt bơ tỏi.', 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=800', 'cats' => ['appetizers-starters']],
    ['name' => 'Mì Cay Hải Sản 7 Cấp Độ', 'price' => 69000, 'desc' => 'Thách thức vị giác với nước dùng chua cay.', 'image' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?w=800', 'cats' => ['main-dish']],

    // --- Món Việt Truyền Thống ---
    ['name' => 'Phở Bò Gia Truyền', 'price' => 55000, 'desc' => 'Nước dùng trong veo, ngọt thanh từ xương ống.', 'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800', 'cats' => ['main-dish']],
    ['name' => 'Bún Bò Huế Đặc Biệt', 'price' => 65000, 'desc' => 'Hương vị đậm đà chuẩn gốc cố đô.', 'image' => 'https://images.unsplash.com/photo-1583095117189-70858f35eec4?w=800', 'cats' => ['main-dish']],
    ['name' => 'Bánh Mì Sài Gòn', 'price' => 35000, 'desc' => 'Nhân pate, chả lụa và thịt nướng thơm lừng.', 'image' => 'https://images.unsplash.com/photo-1606131731446-5568d87113aa?w=800', 'cats' => ['main-dish']],
    ['name' => 'Cơm Tấm Sườn Bì Chả', 'price' => 65000, 'desc' => 'Sườn nướng mật ong ăn kèm mắm chua ngọt.', 'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800', 'cats' => ['main-dish']],
    ['name' => 'Gỏi Cuốn Tôm Thịt', 'price' => 45000, 'desc' => 'Tôm tươi, rau sống cuốn bánh tráng mỏng.', 'image' => 'https://images.unsplash.com/photo-1539136788836-5699e78bac75?w=800', 'cats' => ['appetizers-starters']],
    ['name' => 'Cháo Ếch Singapore', 'price' => 115000, 'desc' => 'Ếch kho thố đất cay nồng hấp dẫn.', 'image' => 'https://images.unsplash.com/photo-1547928576-a4a33237ce35?w=800', 'cats' => ['main-dish']],
    ['name' => 'Bún Chả Hà Nội', 'price' => 60000, 'desc' => 'Chả nướng than hoa thơm nức mũi.', 'image' => 'https://images.unsplash.com/photo-1564671190095-938162922797?w=800', 'cats' => ['main-dish']],
    ['name' => 'Bánh Xèo Miền Tây', 'price' => 55000, 'desc' => 'Vỏ bánh vàng giòn đầy ắp tôm thịt.', 'image' => 'https://images.unsplash.com/photo-1624300627238-993739f731c1?w=800', 'cats' => ['main-dish']],
    ['name' => 'Nem Công Chả Phượng', 'price' => 250000, 'desc' => 'Món ăn cung đình trang trí cầu kỳ.', 'image' => 'https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?w=800', 'cats' => ['main-dish', 'dinner']],
    ['name' => 'Cơm Chiên Dương Châu', 'price' => 75000, 'desc' => 'Cơm chiên tơi xốp cùng lạp xưởng và đậu hà lan.', 'image' => 'https://images.unsplash.com/photo-1512058560366-cd242d4536ee?w=800', 'cats' => ['main-dish']],

    // --- Đồ Uống & Tráng Miệng ---
    ['name' => 'Trà Sữa Trân Châu', 'price' => 55000, 'desc' => 'Trà đậm đà cùng trân châu đen dai giòn.', 'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800', 'cats' => ['drinks', 'beverage']],
    ['name' => 'Cà Phê Muối Đặc Sản', 'price' => 35000, 'desc' => 'Vị mặn nhẹ hòa quyện cùng béo ngậy.', 'image' => 'https://images.unsplash.com/photo-1541167760496-16295cb7d721?w=800', 'cats' => ['drinks', 'beverage']],
    ['name' => 'Nước Ép Trái Cây Mix', 'price' => 45000, 'desc' => 'Vitamin tự nhiên từ cam và cà rốt.', 'image' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=800', 'cats' => ['drinks', 'beverage']],
    ['name' => 'Kem Trái Dừa', 'price' => 45000, 'desc' => 'Kem dừa mát lạnh rắc thêm lạc rang.', 'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800', 'cats' => ['dessert']],
    ['name' => 'Bánh Crepe Sầu Riêng', 'price' => 75000, 'desc' => 'Lớp vỏ bánh mỏng cuộn kem sầu riêng.', 'image' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=800', 'cats' => ['dessert']],
    ['name' => 'Trà Đào Cam Sả', 'price' => 45000, 'desc' => 'Giải nhiệt mùa hè cực kỳ sảng khoái.', 'image' => 'https://images.unsplash.com/photo-1499638673689-79a0b5115d87?w=800', 'cats' => ['drinks', 'beverage']],
    ['name' => 'Bánh Tiramisu Ý', 'price' => 65000, 'desc' => 'Hương vị cà phê và cacao quyến rũ.', 'image' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800', 'cats' => ['dessert']],
    ['name' => 'Sinh Tố Bơ Sáp', 'price' => 50000, 'desc' => 'Bơ sáp Đắk Lắk béo ngậy xay cùng sữa đặc.', 'image' => 'https://images.unsplash.com/photo-1502741224143-90386d7f8c82?w=800', 'cats' => ['drinks', 'beverage']],
    ['name' => 'Chè Thái Sầu Riêng', 'price' => 40000, 'desc' => 'Món chè đầy màu sắc với thạch và trái cây.', 'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=800', 'cats' => ['dessert']],
    ['name' => 'Tàu Hủ Trân Châu Đường Đen', 'price' => 35000, 'desc' => 'Mềm mịn tan trong miệng cùng sốt đường đen.', 'image' => 'https://images.unsplash.com/photo-1567171466295-4afa58141217?w=800', 'cats' => ['dessert']],
];

echo "🚀 Đang kiểm tra và nhập liệu 30 món ăn chất lượng cao...\n";

foreach ($foods as $food) {
    // 1. Kiểm tra ảnh có phản hồi không (Bỏ qua nếu lỗi)
    $ch = curl_init($food['image']);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) {
        echo "⚠️ Ảnh của món " . $food['name'] . " không phản hồi, đang dùng ảnh dự phòng...\n";
        $food['image'] = 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800'; // Ảnh Food dự phòng
    }

    // 2. Kiểm tra trùng lặp
    $existing = new WP_Query([
        'post_type' => 'food_manager',
        'title' => $food['name'],
        'post_status' => 'publish'
    ]);

    if ($existing->have_posts()) {
        $post_id = $existing->posts[0]->ID;
        echo "🔄 Cập nhật món: " . $food['name'] . "\n";
    } else {
        $post_id = wp_insert_post([
            'post_title'    => $food['name'],
            'post_content'  => $food['desc'],
            'post_status'   => 'publish',
            'post_type'     => 'food_manager',
        ]);
        echo "✅ Thêm mới: " . $food['name'] . "\n";
    }

    if ($post_id) {
        // Luôn cập nhật giá và ảnh mới nhất (giá chuẩn VND numeric)
        update_post_meta($post_id, '_food_price', (int)$food['price']);
        update_post_meta($post_id, '_food_banner', $food['image']);
        update_post_meta($post_id, '_food_rating', '4.8');
        
        // Gán danh mục cho món ăn
        if (isset($food['cats'])) {
            wp_set_object_terms($post_id, $food['cats'], 'food_manager_category');
        }
    }
}

echo "✨ Hoàn tất 30 món ăn 'Siêu chất' cho FoodGo!\n";
