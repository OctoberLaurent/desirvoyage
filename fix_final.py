import os
import re

fixes = 0
for root, dirs, files in os.walk('src'):
    dirs[:] = [d for d in dirs if d != 'vendor']
    for fname in files:
        if not fname.endswith('.php'):
            continue
        path = os.path.join(root, fname)
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original = content
        
        # Fix 'final <?php' at beginning of file
        content = re.sub(r'^final (<\?php)', r'\1', content)
        
        # Fix 'final ' inserted in the middle of an identifier
        # Pattern: a word char followed by 'final ' followed by word char
        content = re.sub(r'([a-zA-Z0-9])final ([a-zA-Z0-9])', r'\1\2', content)
        
        if content != original:
            fixes += 1
            print(f'Fixed: {path}')
            with open(path, 'w', encoding='utf-8') as f:
                f.write(content)

print(f'Total files fixed: {fixes}')
