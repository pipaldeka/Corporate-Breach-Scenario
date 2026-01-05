# terraform/modules/docker-scenario/outputs.tf

output "student_id" {
  description = "Student identifier"
  value       = var.student_id
}

output "vlan_id" {
  description = "Assigned VLAN ID"
  value       = var.vlan_id
}

output "network_info" {
  description = "Network configuration"
  value = {
    network_name = docker_network.scenario_network.name
    subnet       = local.network_cidr
    gateway      = local.gateway_ip
  }
}

output "container_info" {
  description = "Container details"
  value = {
    webapp = {
      name = docker_container.webapp.name
      ip   = "10.100.${local.vlan_subnet}.10"
      port = 8000 + var.vlan_id
      url  = "http://localhost:${8000 + var.vlan_id}"
      ssh_port = var.scenario_name == "corporate-breach" ? 2200 + var.vlan_id : null
    }
    database = var.use_separate_db ? {
      name = docker_container.database[0].name
      ip   = "10.100.${local.vlan_subnet}.11"
    } : {
      name = "N/A (embedded in webapp)"
      ip   = "localhost"
    }
  }
}

output "flag" {
  description = "Unique flag for this student"
  value       = "FLAG{${random_string.flag.result}}"
  sensitive   = true
}

output "access_info" {
  description = "How to access the scenario"
  value = {
    web_url    = "http://localhost:${8000 + var.vlan_id}"
    ssh_access = var.scenario_name == "corporate-breach" ? "ssh webadmin@localhost -p ${2200 + var.vlan_id}" : "N/A"
    target_ip  = "10.100.${local.vlan_subnet}.10"
    expires_at = formatdate("YYYY-MM-DD'T'hh:mm:ssZ", timeadd(timestamp(), "${var.session_duration}h"))
  }
}
