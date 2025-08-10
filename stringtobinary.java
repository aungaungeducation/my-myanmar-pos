String input = editText1.getText().toString(); // e.g. "aung"
StringBuilder binary = new StringBuilder();

for (int i = 0; i < input.length(); i++) {
    int ascii = (int) input.charAt(i); // စာလုံးတစ်လုံးရဲ့ ASCII code
    String binString = Integer.toBinaryString(ascii); // ASCII ကို binary string ပြောင်း
    // 8-bit padding ပြုလုပ်ဖို့ (optional)
    while (binString.length() < 8) {
        binString = "0" + binString;
    }
    binary.append(binString).append(" "); // စာလုံးတိုင်း binary ကြားမှာ space ထည့်
}

textView1.setText(binary.toString());
