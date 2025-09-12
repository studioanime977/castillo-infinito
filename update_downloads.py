import os
import re

# CSS styles to add
css_styles = """
    
    .download-options {
      margin-top: 1rem;
    }
    
    .download-btn {
      background: linear-gradient(45deg, #ff5cad, #ff8a80);
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: bold;
      transition: all 0.3s ease;
      width: 100%;
    }
    
    .download-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 92, 173, 0.4);
    }
    
    .download-links {
      margin-top: 1rem;
      padding: 1rem;
      background: rgba(42, 42, 42, 0.9);
      border-radius: 10px;
      border: 1px solid #333;
    }
    
    .download-link {
      display: block;
      padding: 0.5rem 1rem;
      margin: 0.3rem 0;
      background: rgba(255, 92, 173, 0.1);
      color: #ff5cad;
      text-decoration: none;
      border-radius: 5px;
      border: 1px solid #ff5cad;
      transition: all 0.3s ease;
    }
    
    .download-link:hover {
      background: rgba(255, 92, 173, 0.2);
      transform: translateX(5px);
    }
    
    .download-link.terabox {
      border-color: #4CAF50;
      color: #4CAF50;
    }
    
    .download-link.mediafire {
      border-color: #FF5722;
      color: #FF5722;
    }
    
    .download-link.exe {
      border-color: #2196F3;
      color: #2196F3;
    }
    
    .unavailable {
      display: block;
      padding: 0.5rem 1rem;
      margin: 0.3rem 0;
      background: rgba(128, 128, 128, 0.1);
      color: #888;
      border-radius: 5px;
      border: 1px solid #555;
      font-style: italic;
    }"""

# JavaScript function to add
js_function = """
    
    // Función para mostrar opciones de descarga
    function showDownloadOptions(id) {
      const optionsDiv = document.getElementById(id + '-options');
      const allOptions = document.querySelectorAll('.download-links');
      
      // Cerrar todas las otras opciones
      allOptions.forEach(option => {
        if (option.id !== id + '-options') {
          option.style.display = 'none';
        }
      });
      
      // Toggle la opción actual
      if (optionsDiv.style.display === 'none') {
        optionsDiv.style.display = 'block';
        
        // Mostrar alerta con opciones disponibles
        const availableLinks = optionsDiv.querySelectorAll('.download-link');
        const unavailableLinks = optionsDiv.querySelectorAll('.unavailable');
        
        let message = '📥 Opciones de descarga disponibles:\\n\\n';
        availableLinks.forEach(link => {
          const platform = link.textContent.split(' ')[1];
          message += `✅ ${platform}\\n`;
        });
        
        if (unavailableLinks.length > 0) {
          message += '\\n❌ No disponibles actualmente:\\n';
          unavailableLinks.forEach(span => {
            const platform = span.textContent.split(' ')[1];
            message += `• ${platform}\\n`;
          });
        }
        
        message += '\\n🔗 Serás redirigido al enlace seleccionado.';
        alert(message);
      } else {
        optionsDiv.style.display = 'none';
      }
    }"""

def update_anime_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Skip files that already have the download system
    if 'download-options' in content:
        print(f"Skipping {file_path} - already updated")
        return
    
    # Add CSS styles
    content = re.sub(r'(\.section-title\s*{[^}]+})', r'\1' + css_styles, content)
    
    # Update download links
    def replace_download_link(match):
        full_match = match.group(0)
        img_tag = match.group(1)
        title_tag = match.group(2)
        link_href = match.group(3)
        
        # Generate unique ID based on title
        title_text = re.search(r'<h3>([^<]+)</h3>', title_tag).group(1)
        unique_id = title_text.lower().replace(' ', '-').replace('–', '-').replace('—', '-')
        unique_id = re.sub(r'[^a-z0-9\-]', '', unique_id)
        
        new_content = f"""        <div class="anime-card">
          {img_tag}
          {title_tag}
          <div class="download-options">
            <button onclick="showDownloadOptions('{unique_id}')" class="download-btn">📥 Descargar</button>
            <div id="{unique_id}-options" class="download-links" style="display: none;">
              <a href="{link_href}" target="_blank" class="download-link terabox">📦 TeraBox</a>
              <span class="unavailable">💾 Drive (No disponible)</span>
              <span class="unavailable">📁 Dropbox (No disponible)</span>
              <span class="unavailable">🔥 MediaFire (No disponible)</span>
              <span class="unavailable">💎 Mega (No disponible)</span>
            </div>
          </div>
        </div>"""
        
        return new_content
    
    # Pattern to match anime cards with download links
    pattern = r'<div class="anime-card">\s*(<img[^>]+>)\s*(<h3>[^<]+</h3>)\s*<a href="([^"]+)"[^>]*>Descargar</a>\s*</div>'
    content = re.sub(pattern, replace_download_link, content, flags=re.MULTILINE | re.DOTALL)
    
    # Add JavaScript function
    content = re.sub(r'(\s+// Búsqueda en tiempo real)', js_function + r'\1', content)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"Updated {file_path}")

# List of files to update (excluding already updated ones)
anime_files = [
    'boku-no-hero-academia.html',
    'bungo-stray-dogs.html', 
    'classroom-of-the-elite.html',
    'danmachi.html',
    'dragon-ball-heroes.html',
    'dragon-ball.html',
    'even-given-the-worthless.html',
    'horimiya.html',
    'jujutsu-kaisen.html',
    'kimetsu-no-yaiba.html',
    'kono-oto-tomare.html',
    'mashle.html',
    'mob-psycho-100.html',
    'mushoku-tensei.html',
    'my-hero-academia.html',
    'nanatsu-no-taizai.html',
    'naruto.html',
    'nazo-no-kanojo-x.html',
    'one-punch-man.html',
    'papa-no-iukoto-o-kikinasai.html',
    're-zero.html',
    'sakamoto-days.html',
    'sao-progressive.html',
    'solo-leveling.html',
    'tokyo-ghoul.html',
    'urusei-yatsura.html'
]

animes_dir = r'c:\Users\Admin\Desktop\castillo-infinito\animes'

for file_name in anime_files:
    file_path = os.path.join(animes_dir, file_name)
    if os.path.exists(file_path):
        try:
            update_anime_file(file_path)
        except Exception as e:
            print(f"Error updating {file_name}: {e}")

print("All anime files have been updated with the new download system!")
