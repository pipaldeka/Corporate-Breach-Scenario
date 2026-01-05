student_id = "rahul"
vlan_id    = 119

# User-configurable flag mappings
flag_mappings = [
  {
    location    = "/flags/flag1.txt"
    owner       = "www-data:www-data"
    permissions = "644"
    description = "Web accessible - SQL injection"
  },
  {
    location    = "/home/webadmin/flag2.txt"
    owner       = "webadmin:webadmin"
    permissions = "600"
    description = "SSH user access"
  },
  {
    location    = "/root/.flag3.txt"
    owner       = "root:root"
    permissions = "400"
    description = "Root privilege escalation"
  }
]
