pipeline {
    agent any
    options {
        timestamps()
    }
    environment {
        CI = 'true'
        REGISTRY = credentials('REGISTRY')
        IMAGE_TAG = sh(returnStdout: true, script: "echo '${env.BUILD_TAG}' | sed 's/%2F/-/g'").trim()
    }
    stages {
        stage("Init") {
            steps {
                sh "make init"
            }
        }
        stage("Validate schema") {
            steps {
                sh "make validate-schema"
            }
        }
        stage("Lint") {
            parallel {
                stage("API") {
                    steps {
                        sh "sleep 1"
                    }
                }
                stage("FrontEnd") {
                    steps {
                        sh "sleep 1"
                    }
                }
            }
        }
        stage("Analyze") {
            steps {
                sh "sleep 1"
            }
        }
        stage("Test") {
            parallel {
                stage("API") {
                    steps {
                        sh "make api-test"
                    }
                }
                stage("FrontEnd") {
                    steps {
                        sh "sleep 1"
                    }
                }
            }
        }
        stage("Down") {
            steps {
                sh "make down"
            }
        }
        stage("Build") {
            steps {
                sh "make build"
            }
        }
    }
    post {
        always {
            sh "make down || true"
        }
    }
}