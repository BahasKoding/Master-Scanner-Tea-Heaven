# Tutorial Remote SSH Server Testing untuk VS Code

## Daftar Isi
1. [Persiapan Environment](#persiapan-environment)
2. [Setup SSH Server](#setup-ssh-server)
3. [Konfigurasi SSH Keys](#konfigurasi-ssh-keys)
4. [Testing Koneksi SSH](#testing-koneksi-ssh)
5. [Setup VS Code Remote SSH](#setup-vs-code-remote-ssh)
6. [Testing Development Environment](#testing-development-environment)
7. [Troubleshooting](#troubleshooting)

## Persiapan Environment

### Requirements
- **Local Machine**: Windows/macOS/Linux dengan VS Code terinstall
- **Remote Server**: Linux server (Ubuntu/CentOS/Debian) dengan akses root/sudo
- **Network**: Koneksi internet stabil
- **Extensions**: Remote - SSH extension untuk VS Code

### Tools yang Dibutuhkan
```bash
# Untuk Windows
- OpenSSH Client (built-in Windows 10/11)
- VS Code dengan Remote - SSH extension

# Untuk Linux/macOS
- openssh-client
- VS Code dengan Remote - SSH extension
```

## Setup SSH Server

### 1. Install SSH Server (Ubuntu/Debian)
```bash
# Update package list
sudo apt update

# Install OpenSSH Server
sudo apt install openssh-server -y

# Start dan enable SSH service
sudo systemctl start ssh
sudo systemctl enable ssh

# Check status
sudo systemctl status ssh
```

### 2. Install SSH Server (CentOS/RHEL)
```bash
# Install OpenSSH Server
sudo yum install openssh-server -y
# atau untuk CentOS 8+
sudo dnf install openssh-server -y

# Start dan enable SSH service
sudo systemctl start sshd
sudo systemctl enable sshd

# Check status
sudo systemctl status sshd
```

### 3. Konfigurasi SSH Server
```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Konfigurasi penting:
Port 22                          # Default port (bisa diganti untuk security)
PermitRootLogin no              # Disable root login (recommended)
PasswordAuthentication yes       # Enable password auth (sementara)
PubkeyAuthentication yes        # Enable key-based auth
AuthorizedKeysFile .ssh/authorized_keys

# Restart SSH service setelah perubahan
sudo systemctl restart ssh
```

### 4. Setup Firewall
```bash
# Ubuntu/Debian (ufw)
sudo ufw allow ssh
sudo ufw enable

# CentOS/RHEL (firewalld)
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

## Konfigurasi SSH Keys

### 1. Generate SSH Key Pair (Local Machine)
```bash
# Generate new SSH key
ssh-keygen -t rsa -b 4096 -C "your-email@example.com"

# Lokasi default: ~/.ssh/id_rsa (private) dan ~/.ssh/id_rsa.pub (public)
# Atau buat dengan nama custom:
ssh-keygen -t rsa -b 4096 -f ~/.ssh/my_remote_server -C "testing-server"
```

### 2. Copy Public Key ke Remote Server
```bash
# Method 1: Menggunakan ssh-copy-id
ssh-copy-id username@remote-server-ip

# Method 2: Copy manual
cat ~/.ssh/id_rsa.pub | ssh username@remote-server-ip "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"

# Method 3: Manual upload
# Copy isi file ~/.ssh/id_rsa.pub
# Login ke server dan paste ke ~/.ssh/authorized_keys
```

### 3. Set Proper Permissions di Remote Server
```bash
# Set permissions untuk SSH directory
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
chmod 600 ~/.ssh/id_rsa  # jika ada private key di server
```

## Testing Koneksi SSH

### 1. Test Basic SSH Connection
```bash
# Test dengan password (jika enabled)
ssh username@remote-server-ip

# Test dengan specific key
ssh -i ~/.ssh/my_remote_server username@remote-server-ip

# Test dengan verbose output untuk debugging
ssh -v username@remote-server-ip
```

### 2. Create SSH Config File
```bash
# Edit SSH config file
nano ~/.ssh/config

# Tambahkan konfigurasi:
Host testing-server
    HostName remote-server-ip
    User username
    IdentityFile ~/.ssh/my_remote_server
    Port 22

Host production-server
    HostName prod-server-ip
    User produser
    IdentityFile ~/.ssh/prod_key
    Port 2222
```

### 3. Test dengan SSH Config
```bash
# Connect menggunakan alias
ssh testing-server

# Test file transfer
scp test-file.txt testing-server:~/
```

## Setup VS Code Remote SSH

### 1. Install Remote - SSH Extension
1. Buka VS Code
2. Go to Extensions (Ctrl+Shift+X)
3. Search "Remote - SSH"
4. Install extension dari Microsoft

### 2. Configure Remote Connection
1. Press `Ctrl+Shift+P` (Command Palette)
2. Type "Remote-SSH: Connect to Host..."
3. Select "Configure SSH Hosts..."
4. Choose SSH config file location (biasanya `~/.ssh/config`)

### 3. Add Remote Host Configuration
```bash
# Tambahkan di ~/.ssh/config
Host vscode-testing
    HostName your-server-ip
    User your-username
    IdentityFile ~/.ssh/your-key
    Port 22
    ServerAliveInterval 60
    ServerAliveCountMax 3
```

### 4. Connect to Remote Host
1. Press `Ctrl+Shift+P`
2. Type "Remote-SSH: Connect to Host..."
3. Select your configured host
4. VS Code akan membuka window baru dan install VS Code Server di remote

## Testing Development Environment

### 1. Test Basic Operations
```bash
# Buka terminal di VS Code remote
# Test commands:
pwd
ls -la
whoami
df -h
free -m
```

### 2. Test File Operations
1. Create new file di remote
2. Edit file
3. Save file
4. Test IntelliSense dan code completion

### 3. Setup Development Environment
```bash
# Install development tools di remote server
# Contoh untuk Node.js development:
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installation
node --version
npm --version

# Test dengan project
mkdir test-project
cd test-project
npm init -y
npm install express
```

### 4. Test Extensions
1. Install extensions di remote (akan terinstall di remote server)
2. Test language support
3. Test debugging capabilities

## Troubleshooting

### Common Issues dan Solutions

#### 1. Connection Refused
```bash
# Check if SSH service running
sudo systemctl status ssh

# Check if port is open
netstat -tlnp | grep :22

# Check firewall
sudo ufw status
```

#### 2. Permission Denied (publickey)
```bash
# Check SSH key permissions
ls -la ~/.ssh/
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub

# Check authorized_keys di remote
ssh username@server "ls -la ~/.ssh/"
ssh username@server "cat ~/.ssh/authorized_keys"
```

#### 3. VS Code Server Installation Failed
```bash
# Manual cleanup di remote server
rm -rf ~/.vscode-server

# Check disk space
df -h

# Check internet connectivity dari remote
curl -I https://update.code.visualstudio.com
```

#### 4. Extensions Not Working
```bash
# Check VS Code Server logs
tail -f ~/.vscode-server/data/logs/*/exthost1/exthost.log

# Restart VS Code Server
# Di VS Code: Command Palette > "Remote-SSH: Kill VS Code Server on Host"
```

### Performance Optimization

#### 1. SSH Connection Optimization
```bash
# Tambahkan di ~/.ssh/config
Host *
    ServerAliveInterval 60
    ServerAliveCountMax 3
    TCPKeepAlive yes
    Compression yes
    ControlMaster auto
    ControlPath ~/.ssh/control-%r@%h:%p
    ControlPersist 10m
```

#### 2. VS Code Settings untuk Remote
```json
{
    "remote.SSH.showLoginTerminal": true,
    "remote.SSH.localServerDownload": "auto",
    "remote.SSH.remotePlatform": {
        "your-server": "linux"
    }
}
```

## Testing Checklist

### Pre-Connection Tests
- [ ] SSH service running di remote server
- [ ] Firewall configured properly
- [ ] SSH keys generated dan copied
- [ ] SSH config file created
- [ ] Basic SSH connection works

### VS Code Remote Tests
- [ ] Remote - SSH extension installed
- [ ] Can connect to remote host
- [ ] VS Code Server installed successfully
- [ ] Can open files dan folders
- [ ] Terminal works properly
- [ ] Extensions can be installed

### Development Environment Tests
- [ ] Programming language runtime installed
- [ ] Code completion works
- [ ] Debugging capabilities
- [ ] File operations (create, edit, save, delete)
- [ ] Git operations (if needed)

## Security Best Practices

### 1. SSH Hardening
```bash
# Disable password authentication setelah key setup
sudo nano /etc/ssh/sshd_config
PasswordAuthentication no
ChallengeResponseAuthentication no

# Change default SSH port
Port 2222

# Restart SSH service
sudo systemctl restart ssh
```

### 2. User Management
```bash
# Create dedicated user untuk development
sudo adduser devuser
sudo usermod -aG sudo devuser

# Set up SSH keys untuk user baru
su - devuser
mkdir ~/.ssh
chmod 700 ~/.ssh
# Copy public key ke ~/.ssh/authorized_keys
```

### 3. Monitoring
```bash
# Monitor SSH connections
sudo tail -f /var/log/auth.log

# Check active SSH sessions
who
w
```

## Conclusion

Tutorial ini memberikan panduan lengkap untuk setup dan testing remote SSH server untuk development dengan VS Code. Pastikan untuk:

1. Mengikuti security best practices
2. Test semua functionality sebelum production use
3. Document konfigurasi untuk team members
4. Regular maintenance dan monitoring

Dengan setup yang proper, VS Code Remote SSH memberikan experience development yang seamless antara local dan remote environments. 