# terraform/scenarios/corporate-breach/main.tf

module "corporate_breach" {
  source = "../../modules/docker-scenario"

  student_id     = var.student_id
  vlan_id        = var.vlan_id
  scenario_name  = "corporate-breach"
  scenario_path  = abspath("${path.module}/../../../scenarios/corporate-breach")

  session_duration = 6
  use_separate_db = false
  run_ansible = false
}

# Generate flags dynamically based on number of mappings
resource "random_string" "flags" {
  count   = length(var.flag_mappings)
  length  = 16
  special = false
}

# Build flag configuration dynamically
locals {
  flag_config = [
    for idx, mapping in var.flag_mappings : {
      number      = idx + 1
      content     = "FLAG{${var.student_id}_${random_string.flags[idx].result}}"
      location    = mapping.location
      owner       = mapping.owner
      permissions = mapping.permissions
      description = mapping.description
    }
  ]
}

# Inject flags with custom permissions
resource "null_resource" "inject_flags" {
  depends_on = [module.corporate_breach]

  provisioner "local-exec" {
    command = <<EOT
      ansible-playbook ${path.module}/../../../ansible/playbooks/inject-flags-advanced.yml \
        -e student_id=${var.student_id} \
        -e scenario=corporate-breach \
        -e container_name=corporate-breach-webapp-${var.student_id} \
        -e 'flag_config=${jsonencode(local.flag_config)}'
    EOT
  }

  triggers = {
    student_id = var.student_id
    flags      = jsonencode(local.flag_config)
  }
}

# Save flags to file
resource "local_file" "flag_log" {
  content = jsonencode({
    student_id  = var.student_id
    scenario    = "corporate-breach"
    vlan_id     = var.vlan_id
    flag_config = local.flag_config
    created_at  = timestamp()
  })
  
  filename        = "${path.module}/flags-${var.student_id}.json"
  file_permission = "0600"
}

# Variables
variable "student_id" { 
  type        = string
  description = "Unique student identifier"
}

variable "vlan_id" { 
  type        = number
  description = "VLAN ID for network isolation"
}

variable "flag_mappings" {
  type = list(object({
    location    = string
    owner       = string
    permissions = string
    description = string
  }))
  description = "List of flag configurations with location, owner, permissions"
}

# Outputs
output "scenario_info" {
  value     = module.corporate_breach
  sensitive = true
}

output "access_instructions" {
  value = <<EOT

  ========================================
  Corporate Breach Scenario Deployed!
  ========================================

  Web Access: ${module.corporate_breach.access_info.web_url}
  SSH Access: ${module.corporate_breach.access_info.ssh_access}

  Target IP: ${module.corporate_breach.access_info.target_ip}

  🚩 ${length(local.flag_config)} Flags Configured:
  ${join("\n  ", [for cfg in local.flag_config : "FLAG${cfg.number}: ${cfg.location} (${cfg.owner} ${cfg.permissions}) - ${cfg.description}"])}

  Expires: ${module.corporate_breach.access_info.expires_at}

  ========================================
  EOT
}

output "flags" {
  value = {
    for idx, cfg in local.flag_config : "flag${cfg.number}" => cfg.content
  }
  sensitive = true
}
