<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//echo "<pre>"; print_r(apc_cache_info()); echo "</pre>";
if(apc_clear_cache("user") && apc_clear_cache("system") && apc_clear_cache("opcode") && apc_clear_cache("file")){
    echo "<pre>";echo "CLEARED CACHE";echo "</pre>";
}
//apc_delete("arBlogs");
//echo "<pre>"; print_r(apc_cache_info()); echo "</pre>";?>