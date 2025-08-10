String binaryInput = editText1.getText().toString(); // e.g. "01100001 01110101 01101110 01100111"
String[] binaryTokens = binaryInput.split(" ");
StringBuilder text = new StringBuilder();

for (String b : binaryTokens) {
    int charCode = Integer.parseInt(b, 2); // Binary string ကို decimal int ပြောင်း
    text.append((char) charCode); // int ကို char ပြောင်းပြီး append
}

textView1.setText(text.toString());
