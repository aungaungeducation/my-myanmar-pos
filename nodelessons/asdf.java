String amountStr = _data.get((int)_position).get("value").toString().trim();
if (!amountStr.isEmpty()) {
    try {
        double amount = Double.parseDouble(amountStr);
        if (amount > 99) {
            ain.setText(String.valueOf((long)(_position)));
            num.setText(_data.get((int)_position).get("number").toString());
            ch.setText(_formatted(amountStr).concat(" .Ks"));
            edittext1.setText(_formatted(_data.get((int)_position).get("start").toString()));

            final Typeface tf = Typeface.createFromAsset(getAssets(), "fonts/poppinsbold.ttf");
            ain.setTypeface(tf, 1);
            num.setTypeface(tf, 1);
            ch.setTypeface(tf, 1);
            edittext1.setTypeface(tf, 1);

            edittext1.setFocusableInTouchMode(true);
            edittext1.requestFocus();

            edittext1.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                }

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    edittext1.setTypeface(tf, 1);
                    _data.get((int)_position).put("start", edittext1.getText().toString());

                    int posi = lm.size() - 1;
                    int len = lm.size();
                    su = 0;
                    for (int _repeat44 = 0; _repeat44 < len; _repeat44++) {
                        String startVal = lm.get(posi).get("start").toString().trim();
                        if (!startVal.isEmpty()) {
                            try {
                                su += Double.parseDouble(startVal);
                            } catch (NumberFormatException e) {
                            }
                        }
                        posi--;
                    }
                    _aso();
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            if (amount < 5000) {
                ch.setTextColor(0xFFFFD600);
            }

        } else {
            _data.remove((int)(_position));
        }
    } catch (NumberFormatException e) {
        _data.remove((int)(_position));
    }
}