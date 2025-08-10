final BottomSheetDialog dialog = new BottomSheetDialog(MainActivity.this);
LinearLayout layout = new LinearLayout(MainActivity.this);
layout.setOrientation(LinearLayout.VERTICAL);
layout.setPadding(30, 30, 30, 30);

LinearLayout item1 = createMenuItem(R.drawable.usd_2, "account profile");
item1.setOnClickListener(new View.OnClickListener() {
    public void onClick(View v) {
        pro.setClass(getApplicationContext(), ProfActivity.class);
        startActivity(pro);
        dialog.dismiss();
    }
});
layout.addView(item1);

LinearLayout item2 = createMenuItem(R.drawable.usd_5, "account Signout");
item2.setOnClickListener(new View.OnClickListener() {
    public void onClick(View v) {
        data.edit().putString("data", "").commit();
        s2.setClass(getApplicationContext(), IntroActivity.class);
        startActivity(s2);
        dialog.dismiss();
    }
});
layout.addView(item2);

LinearLayout item3 = createMenuItem(R.drawable.usd_7, "played his");
item3.setOnClickListener(new View.OnClickListener() {
    public void onClick(View v) {
        SketchwareUtil.showMessage(getApplicationContext(), "version : 1.0");
        dialog.dismiss();
    }
});
layout.addView(item3);

dialog.setContentView(layout);
dialog.show();