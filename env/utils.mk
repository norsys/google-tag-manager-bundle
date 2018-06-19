MKDIR := mkdir -p
RM := rm -rf
CP := cp -r

define executable
chmod u+x $1
endef

define uninstall
$(RM) $(patsubst uninstall/%,%,$1)
endef

define install
cp env/bin/$(notdir $1) $1
$(call executable,$1)
endef

define export-file
FILE=`mktemp` && trap 'rm -f $$FILE' 0 1 2 3 15 && ( echo 'cat <<EOF'; cat "$1"; echo 'EOF') > $$FILE && export ARGUMENTS='$$@' && $(RM) $2 && . $$FILE > $2
endef

define locate-binary
$(or $(shell which $1),$(error \`$1\` is not in \`$(PATH)\`, please install it!))
endef
