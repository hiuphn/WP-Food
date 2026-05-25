<?php
/**
 * FoodGo Product Variations System
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Helper to get default variant information for grid displays
function foodgo_get_default_variant_data($post_id) {
    $title = get_the_title($post_id);
    $price = get_post_meta($post_id, '_food_price', true);
    
    $variants = get_post_meta($post_id, '_food_variants', true);
    
    if (!empty($variants) && is_array($variants)) {
        $default_vars = array();
        $extra_price = 0;
        foreach ($variants as $group) {
            if (!empty($group['options']) && is_array($group['options'])) {
                $default_vars[] = $group['options'][0]['name'];
                $extra_price += $group['options'][0]['price'];
            }
        }
        if (!empty($default_vars)) {
            $title .= ' (' . implode(', ', $default_vars) . ')';
            $price += $extra_price;
        }
    }
    
    return array(
        'name' => $title,
        'price' => intval($price)
    );
}

// 1. Thêm Meta Box Biến thể
add_action('add_meta_boxes', 'foodgo_add_variants_meta_box');
function foodgo_add_variants_meta_box() {
    add_meta_box(
        'foodgo_variants_meta_box',
        'Biến thể & Tùy chọn Món ăn (FoodGo)',
        'foodgo_render_variants_meta_box',
        'food_manager',
        'normal',
        'high'
    );
}

// 2. Render Meta Box Biến thể
function foodgo_render_variants_meta_box($post) {
    $variants_json = get_post_meta($post->ID, '_food_variants', true);
    if (empty($variants_json)) {
        $variants_json = '[]';
    }
    if (is_array($variants_json)) {
        $variants_json = json_encode($variants_json, JSON_UNESCAPED_UNICODE);
    }
    ?>
    <div id="foodgo-variants-app" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; padding: 10px;">
        <textarea name="foodgo_variants_json" id="foodgo_variants_json" style="display:none;"><?php echo esc_textarea($variants_json); ?></textarea>
        
        <div id="foodgo-groups-container"></div>
        
        <button type="button" id="foodgo-add-group-btn" class="button button-primary" style="margin-top: 15px; height: auto; padding: 8px 16px; font-size: 13px; font-weight: 600;">
            + Thêm nhóm lựa chọn (Size, Topping, Mức cay...)
        </button>
    </div>
    
    <style>
        .fg-group-card {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .fg-group-header {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f1;
        }
        .fg-group-name {
            font-size: 14px;
            font-weight: 700;
            padding: 6px 10px;
            width: 250px;
        }
        .fg-group-type {
            padding: 6px 10px;
        }
        .fg-options-title {
            font-size: 13px;
            font-weight: 600;
            color: #646970;
            margin-bottom: 10px;
        }
        .fg-option-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 8px;
        }
        .fg-option-name {
            width: 200px;
            padding: 5px 8px;
        }
        .fg-option-price {
            width: 120px;
            padding: 5px 8px;
        }
        .fg-delete-btn {
            background: #fcf0f1;
            border: 1px solid #d63638;
            color: #d63638;
            border-radius: 4px;
            cursor: pointer;
            padding: 4px 8px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .fg-delete-btn:hover {
            background: #d63638;
            color: #fff;
        }
        .fg-delete-group-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .fg-add-option-btn {
            margin-top: 5px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        let container = $('#foodgo-groups-container');
        let jsonInput = $('#foodgo_variants_json');
        
        let groups = [];
        try {
            groups = JSON.parse(jsonInput.val() || '[]');
        } catch(e) {
            groups = [];
        }
        
        if (!Array.isArray(groups)) {
            groups = [];
        }
        
        function updateJSON() {
            jsonInput.val(JSON.stringify(groups));
        }
        
        function render() {
            container.empty();
            groups.forEach((group, gIndex) => {
                let groupCard = $(`
                    <div class="fg-group-card" data-index="${gIndex}">
                        <button type="button" class="fg-delete-btn fg-delete-group-btn" data-action="delete-group">Xóa nhóm</button>
                        <div class="fg-group-header">
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:5px; font-size:12px;">Tên nhóm lựa chọn:</label>
                                <input type="text" class="fg-group-name" value="${escapeHtml(group.name)}" placeholder="Ví dụ: Kích cỡ, Topping, Mức cay...">
                            </div>
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:5px; font-size:12px;">Loại lựa chọn:</label>
                                <select class="fg-group-type">
                                    <option value="single" ${group.type === 'single' ? 'selected' : ''}>Chọn một (Radio)</option>
                                    <option value="multiple" ${group.type === 'multiple' ? 'selected' : ''}>Chọn nhiều (Checkbox)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <div class="fg-options-title">Danh sách tùy chọn:</div>
                            <div class="fg-options-list"></div>
                            <button type="button" class="button fg-add-option-btn" data-action="add-option">+ Thêm tùy chọn</button>
                        </div>
                    </div>
                `);
                
                let optionsList = groupCard.find('.fg-options-list');
                group.options.forEach((opt, oIndex) => {
                    let optRow = $(`
                        <div class="fg-option-row" data-index="${oIndex}">
                            <input type="text" class="fg-option-name" value="${escapeHtml(opt.name)}" placeholder="Ví dụ: Size L, Trân châu...">
                            <input type="number" class="fg-option-price" value="${opt.price}" placeholder="Giá cộng thêm (đ)" min="0">
                            <span style="color:#646970; font-size:12px;">₫</span>
                            <button type="button" class="fg-delete-btn" data-action="delete-option">×</button>
                        </div>
                    `);
                    
                    optRow.find('.fg-option-name').on('input', function() {
                        groups[gIndex].options[oIndex].name = $(this).val();
                        updateJSON();
                    });
                    
                    optRow.find('.fg-option-price').on('input', function() {
                        groups[gIndex].options[oIndex].price = parseInt($(this).val()) || 0;
                        updateJSON();
                    });
                    
                    optRow.find('[data-action="delete-option"]').on('click', function() {
                        groups[gIndex].options.splice(oIndex, 1);
                        updateJSON();
                        render();
                    });
                    
                    optionsList.append(optRow);
                });
                
                groupCard.find('.fg-group-name').on('input', function() {
                    groups[gIndex].name = $(this).val();
                    updateJSON();
                });
                
                groupCard.find('.fg-group-type').on('change', function() {
                    groups[gIndex].type = $(this).val();
                    updateJSON();
                });
                
                groupCard.find('[data-action="add-option"]').on('click', function() {
                    groups[gIndex].options.push({ name: '', price: 0 });
                    updateJSON();
                    render();
                });
                
                groupCard.find('[data-action="delete-group"]').on('click', function() {
                    groups.splice(gIndex, 1);
                    updateJSON();
                    render();
                });
                
                container.append(groupCard);
            });
        }
        
        $('#foodgo-add-group-btn').on('click', function() {
            groups.push({
                name: '',
                type: 'single',
                options: [{ name: '', price: 0 }]
            });
            updateJSON();
            render();
        });
        
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        
        render();
    });
    </script>
    <?php
}

// 3. Lưu Meta Box Biến thể
add_action('save_post', 'foodgo_save_variants_meta_box');
function foodgo_save_variants_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Check post type
    if (get_post_type($post_id) !== 'food_manager') return;
    
    if (isset($_POST['foodgo_variants_json'])) {
        $variants_raw = wp_unslash($_POST['foodgo_variants_json']);
        $variants_array = json_decode($variants_raw, true);
        
        $clean_variants = array();
        if (is_array($variants_array)) {
            foreach ($variants_array as $group) {
                if (empty($group['name'])) continue;
                $clean_group = array(
                    'name' => sanitize_text_field($group['name']),
                    'type' => sanitize_text_field($group['type']),
                    'options' => array()
                );
                
                if (is_array($group['options'])) {
                    foreach ($group['options'] as $opt) {
                        if (empty($opt['name'])) continue;
                        $clean_group['options'][] = array(
                            'name' => sanitize_text_field($opt['name']),
                            'price' => intval($opt['price'])
                        );
                    }
                }
                
                if (!empty($clean_group['options'])) {
                    $clean_variants[] = $clean_group;
                }
            }
        }
        
        update_post_meta($post_id, '_food_variants', $clean_variants);
    }
}
